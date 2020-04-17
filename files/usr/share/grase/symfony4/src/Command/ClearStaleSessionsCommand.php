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
 * Cron task to find all sessions that appear to have timed out but haven't been properly closed
 * Any sessions that hasn't had a radius accounting update in 15 minutes will be considered stale
 */
class ClearStaleSessionsCommand extends Command
{
    protected static $defaultName = 'grase:cron:clearStaleSessions';

    /** @var EntityManagerInterface */
    private $em;

    /** @var LoggerInterface */
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
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $auditLogger, LoggerInterface $logger, TranslatorInterface $translator)
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
            ->setDescription('Clear stale radius sessions (15 minutes without an update accounting packet)')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new GraseConsoleStyle($input, $output);

        $sql = "UPDATE radacct
                SET
                AcctTerminateCause='Admin-Reset',
                AcctStopTime = FROM_UNIXTIME(UNIX_TIMESTAMP(AcctStartTime) + AcctSessionTime)
                WHERE
                (AcctStopTime IS NULL OR AcctStopTime = 0)
                AND
                TIME_TO_SEC(
                            TIMEDIFF(
                                     NOW(),
                                     ADDTIME(
                                             AcctStartTime,
                                             SEC_TO_TIME(AcctSessionTime)
                                            )
                                     )
                            ) > 900";
        $updatedSessions = $this->em->getConnection()->exec($sql);

        if ($updatedSessions) {
            $this->auditLogger->info(
                'grase.cron.audit.clearStaleSessions',
                ['updatedSessions' => $updatedSessions]
            );
            $io->success(
                $this->translator->trans(
                    'grase.cron.output.clearStaleSessions',
                    ['updatedSessions' => $updatedSessions]
                )
            );
        }
    }
}
