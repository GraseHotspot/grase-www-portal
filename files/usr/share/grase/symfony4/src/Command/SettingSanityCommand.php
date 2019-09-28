<?php

namespace App\Command;

use App\Util\SettingsSanity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SettingSanityCommand
 *
 * This command just runs the Sanity checking on the settings in the Grase database. It'll reset to default any that are
 * invalid, and it'll upgrade some older settings to the new JSON format. It'll also remove obsolete settings.
 */
class SettingSanityCommand extends Command
{
    protected static $defaultName = 'grase:settings-validate';

    /** @var SettingsSanity */
    private $settingsSanity;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * SettingSanityCommand constructor.
     *
     * @param SettingsSanity      $settingsSanity
     * @param TranslatorInterface $translator
     */
    public function __construct(SettingsSanity $settingsSanity, TranslatorInterface $translator)
    {
        parent::__construct();
        $this->settingsSanity = $settingsSanity;
        $this->translator     = $translator;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $changedSettings = $this->settingsSanity->sanityCheckSettings();
        $output->writeln(
            $this->translator->trans(
                "grase.command.settingsSanity.updatedSettings",
                ['updatedSettings' => $changedSettings]
            )
        );
    }
}
