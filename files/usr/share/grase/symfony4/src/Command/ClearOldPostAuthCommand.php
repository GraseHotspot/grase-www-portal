<?php

namespace App\Command;

use App\Util\GraseConsoleStyle;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Cron task to limit how long the postauth table gets by deleting anything older than the previous
 * month.
 */
class ClearOldPostAuthCommand extends Command
{
    protected static $defaultName = 'grase:cron:clearOldPostAuth';

    /** @var EntityManagerInterface */
    private $em;

    /** @var Logger */
    private $auditLogger;

    /** @var LoggerInterface */
    private $logger;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Logger                 $auditLogger
     * @param LoggerInterface        $logger
     * @param TranslatorInterface    $translator
     */
    public function __construct(EntityManagerInterface $entityManager, Logger $auditLogger, LoggerInterface $logger, TranslatorInterface $translator)
    {
        parent::__construct();
        $this->em = $entityManager;
        $this->auditLogger = $auditLogger;
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Clear Old Post Auth table entries (older than the previous month)')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new GraseConsoleStyle($input, $output);

        $twoMonthsAgo = strftime('%Y-%m-%d', strtotime('first day of -1 months'));
        $sql = 'DELETE FROM radpostauth WHERE AuthDate <  :twoMonthsAgo';
        $query = $this->em->getConnection()->prepare($sql);
        $result = $query->execute(['twoMonthsAgo' => $twoMonthsAgo]);

        if ($result) {
            $oldPostAuthSessions = $query->rowCount();
            if ($oldPostAuthSessions > 0) {
                $this->auditLogger->info(
                    'grase.cron.audit.clearOldPostAuth',
                    ['oldPostAuthSessions' => $oldPostAuthSessions]
                );
                $io->success(
                    $this->translator->trans(
                        'grase.cron.output.clearOldPostAuth',
                        ['oldPostAuthSessions' => $oldPostAuthSessions]
                    )
                );
            }
        } else {
            $this->logger->error($this->translator->trans('grase.cron.log.clearOldPostAuth.error'), ['error' => $query->errorInfo()]);
            $io->error($this->translator->trans('grase.cron.output.clearOldPostAuth.error'));
        }
    }
}
