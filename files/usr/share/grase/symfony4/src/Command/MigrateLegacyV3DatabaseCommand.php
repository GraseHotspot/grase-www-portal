<?php

namespace App\Command;

use App\Util\GraseConsoleStyle;
use App\Util\SqlFileImporter;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This command takes 2 database backups (radius/radmin) and imports them into the current database
 * overriding all current data. It then runs the required database migrations to take us to a V4
 * database structure *
 */
class MigrateLegacyV3DatabaseCommand extends Command
{
    protected static $defaultName = 'grase:migrate-v3-backup';

    /** @var TranslatorInterface */
    private $translator;

    /** @var SqlFileImporter */
    private $sqlFileImporter;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * @param TranslatorInterface $translator
     * @param SqlFileImporter     $sqlFileImporter
     * @param LoggerInterface     $logger
     */
    public function __construct(TranslatorInterface $translator, SqlFileImporter $sqlFileImporter, LoggerInterface $logger)
    {
        parent::__construct();
        $this->translator     = $translator;
        $this->sqlFileImporter = $sqlFileImporter;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Imports V3 backup files overriding the current database')
            ->addArgument(
                'radius_file',
                InputArgument::REQUIRED,
                'Location of Radius database Backup File'
            )
            ->addArgument(
                'radmin_file',
                InputArgument::REQUIRED,
                'Location of Radmin database Backup File'
            )
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new GraseConsoleStyle($input, $output);
        $radiusFilename = $input->getArgument('radius_file');
        $radminFilename = $input->getArgument('radmin_file');

        // TODO add logger steps

        if (!file_exists($radiusFilename)) {
            $io->error($this->translator->trans(
                'grase.command.migrate-v3.error.radiusFileMissing',
                ['filename' => $radiusFilename]
            ));

            return -1;
        }
        if (!file_exists($radminFilename)) {
            $io->error($this->translator->trans(
                'grase.command.migrate-v3.error.radminFileMissing',
                ['filename' => $radminFilename]
            ));

            return -1;
        }

        if (!$this->sqlFileImporter->confirmBackupFile($radminFilename)) {
            $io->error($this->translator->trans(
                'grase.command.migrate-v3.error.backupFileInvalid',
                ['filename' => $radminFilename]
            ));

            return -1;
        }

        if (!$this->sqlFileImporter->confirmBackupFile($radiusFilename)) {
            $io->error($this->translator->trans(
                'grase.command.migrate-v3.error.backupFileInvalid',
                ['filename' => $radiusFilename]
            ));

            return -1;
        }

        $io->caution($this->translator->trans('grase.command.migrate-v3.warning-override-database'));

        $answer = $io->confirm($this->translator->trans('grase.command.migrate-v3.confirm-import'), false);

        if (!$answer) {
            $io->success($this->translator->trans('grase.command.migrate-v3.noChangesMade'));

            return 0;
        }

        $io->note($this->translator->trans('grase.command.migrate-v3.startClearDatabase'));
        $this->sqlFileImporter->eraseDatabase();

        $io->note($this->translator->trans(
            'grase.command.migrate-v3.startImportBackupFile',
            ['filename' => $radminFilename]
        ));
        $result = $this->sqlFileImporter->importSqlFile($radminFilename);
        if (!$result) {
            $io->error($this->translator->trans('grase.command.migrate-v3.error.importFailed'));

            return -1;
        }

        $io->note($this->translator->trans(
            'grase.command.migrate-v3.startImportBackupFile',
            ['filename' => $radiusFilename]
        ));
        $result = $this->sqlFileImporter->importSqlFile($radiusFilename);
        if (!$result) {
            $io->error($this->translator->trans('grase.command.migrate-v3.error.importFailed'));

            return -1;
        }

        $io->success($this->translator->trans('grase.command.migrate-v3.success.completed'));

        // Database migrations
        $this->logger->info($this->translator->trans('grase.command.migrate-v3.startMigrations'));
        $io->note($this->translator->trans('grase.command.migrate-v3.startMigrations'));

        $cmd = $this->getApplication()->find('doctrine:migrations:migrate');
        $cmdInput = new ArrayInput([
            'command' => 'doctrine:migrations:migrate',
        ]);
        $cmdInput->setInteractive(false);

        try {
            $cmd->run($cmdInput, $output);
        } catch (\Exception $e) {
            $io->error($this->translator->trans('grase.command.migrate-v3.error.doctrineMigrations'));
            $this->logger->error(
                $this->translator->trans('grase.command.migrate-v3.error.doctrineMigrations'),
                [
                    'exception' => $e->getMessage(),
                ]
            );

            return -1;
        }

        // Settings validation
        $this->logger->info($this->translator->trans('grase.command.migrate-v3.startSettingsValidate'));
        $io->note($this->translator->trans('grase.command.migrate-v3.startSettingsValidate'));

        $cmd = $this->getApplication()->find('grase:settings-validate');
        $arguments = [
            'command' => 'grase:settings-validate',
        ];

        try {
            $cmd->run(new ArrayInput($arguments), $output);
        } catch (\Exception $e) {
            $io->error($this->translator->trans(
                'grase.command.migrate-v3.error.settingsValidation'
            ));
            $this->logger->error(
                $this->translator->trans('grase.command.migrate-v3.error.settingsValidation'),
                [
                    'exception' => $e,
                ]
            );

            return -1;
        }

        return 0;
    }
}
