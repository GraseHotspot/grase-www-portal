<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Cron runner command to run the rest of the cron components
 */
class GraseCronRunnerCommand extends Command
{
    protected static $defaultName = 'grase:cron:runner';

    /** @var EntityManagerInterface */
    private $em;

    /** @var Logger */
    private $auditLogger;

    /** @var LoggerInterface */
    private $logger;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * GraseCronRunnerCommand constructor.
     *
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
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Cron Runner to automatically run other cron jobs')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $returnCode = 0;

        $commands = [
            'Activate Expiry After First Login' => 'grase:cron:activateExpireAfterLogin',
            'Clear Stale Sessions' => 'grase:cron:clearStaleSessions',
            'Delete Expired Users' => 'grase:cron:deleteExpiredUsers',
            'Clear old postAuth rows' => 'grase:cron:clearOldPostAuth',
        ];

        foreach ($commands as $name => $command) {
            $cmd = $this->getApplication()->find($command);

            $arguments = [
                'command' => $command,
            ];

            $this->logger->info($this->translator->trans('grase.cron.runner.starting.job'), ['name' => $name]);

            try {
                $cmd->run(new ArrayInput($arguments), $output);
            } catch (\Exception $e) {
                $io->error($this->translator->trans(
                    'grase.cron.runner.error.job',
                    ['name' => $name]
                ));
                $this->logger->error('Cron runner job failed', [
                    'name' => $name,
                    'exception' => $e,
                ]);
                $returnCode = 1;
            }
        }


        return $returnCode;
    }
}
