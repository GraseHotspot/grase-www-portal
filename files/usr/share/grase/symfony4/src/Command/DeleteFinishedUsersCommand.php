<?php

namespace App\Command;

use App\Entity\Radius\User;
use App\Entity\UpdateUserData;
use App\Util\GraseConsoleStyle;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Command for deleting all users who have used up their Data or Time limits
 */
class DeleteFinishedUsersCommand extends Command
{
    protected static $defaultName = 'grase:deleteFinishedUsers';

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
            ->setDescription('Delete user accounts that have finished all Data or Time limits')
            ->setHelp('Delete user accounts that have finished all Data or Time limits (after the monthly compaction has occurred)')
            ->addOption(
                'really-delete',
                null,
                InputOption::VALUE_NONE,
                'Actually delete the user accounts instead of just showing who would be deleted'
            )
            ->addOption(
                'delete-out-of-data-users',
                'd',
                InputOption::VALUE_NONE,
                'Select users who have used up all their data quota'
            )
            ->addOption(
                'delete-out-of-time-users',
                't',
                InputOption::VALUE_NONE,
                'Select users who have used up all their time'
            )

        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new GraseConsoleStyle($input, $output);
        // Find the list of users, then run PHP code to do the actual deletes
        $reallyDelete = $input->getOption('really-delete');
        $outOfData = $input->getOption('delete-out-of-data-users');
        $outOfTime = $input->getOption('delete-out-of-time-users');

        // We need at least 1 of the options, otherwise show an error and display the help
        if (!$outOfData && !$outOfTime) {
            $io->error(
                $this->translator->trans('grase.command.finished_users.no_option')
            );
            $help = new HelpCommand();
            $help->setCommand($this);
            $io->write($help->run($input, $output));

            return 1;
        }

        if (!$reallyDelete) {
            $io->warning(
                $this->translator->trans(
                    'grase.command.output.dry-run-warning'
                )
            );
        }

        if ($outOfTime) {
            $this->processOutOfTime($io, $reallyDelete);
        }

        if ($outOfData) {
            $this->processOutOfData($io, $reallyDelete);
        }

        return 0;
    }

    /**
     * Find all Out Of Data users and Delete them
     *
     * @param GraseConsoleStyle $io
     * @param bool              $reallyDelete
     */
    private function processOutOfData(GraseConsoleStyle $io, $reallyDelete = false)
    {
        /** @var User[] $users */
        $users = $this->em->getRepository(User::class)->findOutOfDataUsers();
        foreach ($users as $user) {
            $io->text(
                $this->translator->trans(
                    'grase.command.output.deleteOutOfDataUser',
                    ['user' => $user->getUsername()]
                )
            );
            if ($reallyDelete) {
                $this->auditLogger->info(
                    'grase.command.audit.deleteOutOfDataUser',
                    ['user' => $user->getUsername()]
                );
                UpdateUserData::deleteUser($user, $this->em);
            }
        }
    }

    /**
     * Find all out of Time users and delete them
     *
     * @param GraseConsoleStyle $io
     * @param bool              $reallyDelete
     */
    private function processOutOfTime(GraseConsoleStyle $io, $reallyDelete = false)
    {
        /** @var User[] $users */
        $users = $this->em->getRepository(User::class)->findOutOfTimeUsers();
        foreach ($users as $user) {
            $io->note(
                $this->translator->trans(
                    'grase.command.output.deleteOutOfTimeUser',
                    ['user' => $user->getUsername()]
                )
            );
            if ($reallyDelete) {
                $this->auditLogger->info(
                    'grase.command.audit.deleteOutOfTimeUser',
                    ['user' => $user->getUsername()]
                );
                UpdateUserData::deleteUser($user, $this->em);
            }
        }
    }
}
