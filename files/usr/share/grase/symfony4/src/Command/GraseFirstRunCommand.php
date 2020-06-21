<?php

namespace App\Command;

use ApiPlatform\Core\DataProvider\Pagination;
use App\Entity\Radmin\User;
use App\Entity\Setting;
use App\Util\GraseConsoleStyle;
use App\Util\GraseUtil;
use App\Util\SettingsUtils;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\RuntimeException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

class GraseFirstRunCommand extends Command
{
    protected static $wizardVersion = 4.0;
    protected static $defaultName = 'grase:first-run';

    /** @var SettingsUtils */
    private $settingsUtils;

    /** @var EntityManagerInterface */
    private $em;

    /** @var LoggerInterface */
    private $auditLogger;

    /** @var LoggerInterface */
    private $logger;

    /** @var TranslatorInterface */
    private $translator;

    /** @var bool Set to true when we're forcing a run, ask more questions when forcing */
    private $force = false;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;


    private $firstRunWizardVersion;

    public function __construct(SettingsUtils $settingsUtils, EntityManagerInterface $entityManager, LoggerInterface $auditLogger, LoggerInterface $logger, TranslatorInterface $translator, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->settingsUtils = $settingsUtils;
        $this->em = $entityManager;
        $this->auditLogger = $auditLogger;
        $this->translator = $translator;
        $this->logger = $logger;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct();
}


    protected function configure()
    {
        $this
            ->setDescription('First run wizard for initial setup')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force wizard to regardless of first run status')
            ->addOption('lan-if', null, InputOption::VALUE_REQUIRED, 'LAN Network Interface')
            ->addOption('wan-if', null, InputOption::VALUE_REQUIRED, 'WAN Network Interface')
            ->addOption('random-admin-password', null, InputOption::VALUE_NONE, 'Generate a random password for the admin user instead of prompting for a password')

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new GraseConsoleStyle($input, $output);
        $this->firstRunWizardVersion = $this->settingsUtils->getSettingValue(Setting::FIRST_RUN_WIZARD_VERSION);


        // TODO detect if we're interactive ($input->isInteractive())

        if ($this->firstRunWizardVersion < self::$wizardVersion || $input->getOption('force')) {
            $this->runWizard($io, $input);
            // TODO Set setting version at success
        } else {
            $io->warning($this->translator->trans('grase.command.first-run.skipping'));
        }
        return 0;
    }

    protected function runWizard(OutputStyle $io, InputInterface $input)
    {
        // Questions
        // Set language
        // WAN interface
        // LAN interface
        // IP Address for LAN
        // Set admin password
        $this->setAdminPassword($io, $input);

    }

    protected function setupWAN(OutputStyle $io)
    {

    }

    protected function setupLAN(OutputStyle $io)
    {
        // LAN Interface

        // LAN IP Address
    }

    /**
     * Set the admin users password
     *
     * TODO Maybe allow us to continue on failure by setting a random password?
     *
     * @param OutputStyle $io
     *
     * @return int
     */
    protected function setAdminPassword(OutputStyle $io, InputInterface $input)
    {
        $io->title($this->translator->trans('grase.command.first-run.admin-reset.title'));
        $randomAdminPassword = $input->getOption('random-admin-password');
        // Find out if we already have an admin user
        $adminUser = $this->em->getRepository(User::class)->find('admin');
        // If we already have an admin user, check first (unless it's from an upgrade pre 4.0)
        if ($adminUser && $this->firstRunWizardVersion >= 4.0 && !$randomAdminPassword
            && !$io->confirm($this->translator->trans('grase.command.first-run.admin-reset.confirm'), false)) {
            return 0;
        }

        if (!$adminUser) {
            $adminUser = new User();
            $adminUser->setUsername('admin');

        }

        // Ensure the admin user's role us a superadmin
        $adminUser->setRole('ROLE_SUPERADMIN');

        if ($randomAdminPassword) {
            $password1 = GraseUtil::randomPassword(10);
            $io->note($this->translator->trans('grase.command.first-run.admin-reset.random', ['password' => $password1]));

        } else {

            $failedValidations = 0;

            do {
                $password1 = $io->askHidden($this->translator->trans('grase.command.first-run.admin-reset.password1'));
                $password2 = $io->askHidden($this->translator->trans('grase.command.first-run.admin-reset.password2'));
                $validationResults = $this->validateAdminPassword($password1, $password2);
                if (sizeof($validationResults)) {
                    foreach ($validationResults as $validationResult) {
                        $io->error($validationResult);
                    }
                    $failedValidations++;
                    if ($failedValidations > 5) {
                        throw new RuntimeCommandException(
                            $this->translator->trans('grase.command.first-run.admin-reset.validationLimit')
                        );

                    }
                }
            } while (sizeof($validationResults) !== 0);
        }

        $adminUser->setPassword($this->passwordEncoder->encodePassword($adminUser, $password1));

        $this->em->persist($adminUser);
        $this->em->flush();
        $io->success($this->translator->trans('grase.command.first-run.admin-reset.success'));
        return 1;
    }

    /**
     * Validate the passwords and return an array of errors
     *
     * @param $password1
     * @param $password2
     *
     * @return array
     */
    private function validateAdminPassword($password1, $password2)
    {
        $errors = [];
        $validator = Validation::createValidator();
        $violations = $validator->validate($password1, [new NotBlank(), new Length(['min' => 8, 'max' => 4096]), new NotCompromisedPassword()]);
        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }
        if ($password2 !== $password1) {
            $errors[] = $this->translator->trans('grase.command.first-run.admin-reset.passwords_must_match');
        }

        return ($errors);
    }
}
