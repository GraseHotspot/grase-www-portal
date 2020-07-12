<?php

namespace App\Command;

use App\Entity\Radmin\User;
use App\Entity\Setting;
use App\Util\GraseConsoleStyle;
use App\Util\GraseUtil;
use App\Util\SettingsUtils;
use App\Util\SystemUtils;
use App\Validator\Constraints\SubnetMask;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

class GraseFirstRunCommand extends Command
{
    const WIZARD_VERSION = 4.0;
    protected static $defaultName = 'grase:first-run';

    /** @var SettingsUtils */
    private $settingsUtils;

    /** @var SystemUtils */
    private $systemUtils;

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
        $this->systemUtils = new SystemUtils();
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
        // TODO add options for LAN/WAN interfaces?
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new GraseConsoleStyle($input, $output);

        $this->firstRunWizardVersion = $this->settingsUtils->getSettingValue(Setting::FIRST_RUN_WIZARD_VERSION);

        // TODO detect if we're interactive ($input->isInteractive())

        if ($this->firstRunWizardVersion < self::WIZARD_VERSION || $input->getOption('force')) {
            $this->runWizard($io, $input);
        } else {
            $io->warning($this->translator->trans('grase.command.first-run.skipping'));
        }

        return 0;
    }

    protected function runWizard(OutputStyle $io, InputInterface $input)
    {
        // Questions
        // Set language
        $this->setLocale($io);

        // WAN interface
        $this->setupWAN($io);
        // LAN interface
        $this->setupLAN($io);
        // IP Address for LAN
        // Set admin password
        $this->setAdminPassword($io, $input);

        // TODO set DHCP range?

        // TODO Set setting version at success
        $firstRunWizardSetting = $this->settingsUtils->getSetting(Setting::FIRST_RUN_WIZARD_VERSION);
        if (!$firstRunWizardSetting) {
            $firstRunWizardSetting = new Setting(Setting::FIRST_RUN_WIZARD_VERSION);
        }
        $this->settingsUtils->updateSetting($firstRunWizardSetting, self::WIZARD_VERSION);
    }

    private function setLocale(OutputStyle $io)
    {
        $currentLocale = $this->translator->getLocale();
        $newLocale = $io->ask($this->translator->trans('grase.command.first-run.locale.%current%', ['current' => $currentLocale]), $currentLocale);
        if ($newLocale !== $currentLocale) {
            $this->translator->setLocale($newLocale);
        }
    }

    protected function setupWAN(OutputStyle $io)
    {
        $io->title($this->translator->trans('grase.command.first-run.setup-wan.title'));
        // Find the networkInterfaces that are suitable for this
        $potentialWanInterfaces = $this->systemUtils->getPotentialWanNetworkInterfaces();

        $wanInterfaceSetting = $this->settingsUtils->getSetting(Setting::NETWORK_WAN_INTERFACE);
        if ($wanInterfaceSetting->getValue()) {
            $io->note(
                $this->translator->trans(
                    'grase.command.first-run.setup-wan.current-%wan%',
                    ['wan' => $wanInterfaceSetting->getValue()]
                )
            );
        }

        if (sizeof($potentialWanInterfaces) === 0) {
            $io->error($this->translator->trans('grase.command.first-run.setup-wan.no-available-wan-interfaces.error'));
            $selectedWanInterface = $io->ask($this->translator->trans('grase.command.first-run.setup-wan.no-available-wan-interfaces.manual-input'), $wanInterfaceSetting->getValue());
            if (empty($selectedWanInterface) || $selectedWanInterface === $wanInterfaceSetting->getValue()) {
                $io->error($this->translator->trans('grase.command.first-run.setup-wan.no-available-wan-interfaces.no-change'));

                return;
            }
        } else {
            $selectedWanInterface = $io->choice(
                $this->translator->trans('grase.command.first-run.setup-wan.select-nic'),
                array_keys($potentialWanInterfaces),
                array_keys($potentialWanInterfaces)[0]
            );
        }
        $this->settingsUtils->updateSetting($wanInterfaceSetting, $selectedWanInterface);
    }

    protected function setupLAN(OutputStyle $io)
    {
        $this->setupLanInterface($io);
        $this->setupLanIpAddress($io);
    }

    protected function setupLanInterface(OutputStyle $io)
    {
        // LAN Interface
        $io->title($this->translator->trans('grase.command.first-run.setup-lan.title'));

        // Find the networkInterfaces that are suitable for this (no current IP, no gateway)
        // TODO ensure we filter out the current WAN, and if the current LAN and WAN are the same, throw an error or null one out?
        $potentialLanInterfaces = $this->systemUtils->getPotentialLanNetworkInterfaces();

        $lanInterfaceSetting = $this->settingsUtils->getSetting(Setting::NETWORK_LAN_INTERFACE);
        if ($lanInterfaceSetting->getValue()) {
            $io->note(
                $this->translator->trans(
                    'grase.command.first-run.setup-lan.current-%lan%',
                    ['lan' => $lanInterfaceSetting->getValue()]
                )
            );
        }

        if (sizeof($potentialLanInterfaces) === 0) {
            $io->error($this->translator->trans('grase.command.first-run.setup-lan.no-available-lan-interfaces.error'));
            $selectedLanInterface = $io->ask(
                $this->translator->trans('grase.command.first-run.setup-lan.no-available-lan-interfaces.manual-input'),
                $lanInterfaceSetting->getValue()
            );
            if (empty($selectedLanInterface) || $selectedLanInterface === $lanInterfaceSetting->getValue()) {
                $io->error(
                    $this->translator->trans('grase.command.first-run.setup-lan.no-available-lan-interfaces.no-change')
                );

                return;
            }
        } else {
            $selectedLanInterface = $io->choice(
                $this->translator->trans('grase.command.first-run.setup-lan.select-nic'),
                array_keys($potentialLanInterfaces),
                array_keys($potentialLanInterfaces)[0]
            );
        }
        $this->settingsUtils->updateSetting($lanInterfaceSetting, $selectedLanInterface);
    }

    protected function setupLanIpAddress(OutputStyle $io)
    {
        $io->title($this->translator->trans('grase.command.first-run.setup-lan-ip.title'));

        $lanIpSetting = $this->settingsUtils->getSetting(Setting::NETWORK_LAN_IP);
        $lanNetmaskSetting = $this->settingsUtils->getSetting(Setting::NETWORK_LAN_MASK);
        if ($lanIpSetting->getValue() || $lanNetmaskSetting->getValue()) {
            $io->note(
                $this->translator->trans(
                    'grase.command.first-run.setup-lan-ip.current-%ip%-%mask%',
                    ['ip' => $lanIpSetting->getValue(), 'mask' => $lanNetmaskSetting->getValue()]
                )
            );
        }

        // Do validations
        $failedValidations = 0;
        do {
            $newLanIp = $io->ask(
                $this->translator->trans('grase.command.first-run.setup-lan-ip.ip-question'),
                $lanIpSetting->getValue()
            );
            $newLanMask = $io->ask(
                $this->translator->trans('grase.command.first-run.setup-lan-ip.mask-question'),
                $lanNetmaskSetting->getValue()
            );

            if (strlen($newLanMask) < 8 && is_numeric($newLanMask) && $newLanMask < 30 && $newLanMask > 8) {
                // We have an int CIDR hopefully
                $newLanMask = GraseUtil::CIDRtoMask($newLanMask);
            }

            // IP Validations
            $validator = Validation::createValidator();
            // We can't validate only private, it's not part of PHP's filter_vars, so at least filter out reserved ranges
            $violations = $validator->validate($newLanIp, [new NotBlank(), new Ip(['version' => Ip::V4_NO_RES])], [Ip::V4_NO_RES]);

            // Netmask validations
            $violations->addAll($validator->validate($newLanMask, [new NotBlank(), new SubnetMask()]));

            // TODO ensure IP is in netmask?

            if (sizeof($violations)) {
                foreach ($violations as $violation) {
                    $io->error($violation);
                }
                $failedValidations++;
                if ($failedValidations > 5) {
                    throw new RuntimeCommandException($this->translator->trans('grase.command.first-run.setup-lan-ip.validationLimit'));
                }
            }
        } while (sizeof($violations) !== 0);

        $io->success($this->translator->trans('grase.command.first-run.setup-lan-ip.setting-%ip%-%mask%',
                    ['ip' => $newLanIp, 'mask' => $newLanMask]));

        $this->settingsUtils->updateSetting($lanIpSetting, $newLanIp);
        $this->settingsUtils->updateSetting($lanNetmaskSetting, $newLanMask);
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
            && !$io->confirm($this->translator->trans('grase.command.first-run.admin-reset.confirm'), false)
        ) {
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
                        throw new RuntimeCommandException($this->translator->trans('grase.command.first-run.admin-reset.validationLimit'));
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

        return $errors;
    }
}
