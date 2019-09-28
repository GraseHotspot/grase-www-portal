<?php

namespace App\Command;

use App\Util\SettingsSanity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SettingSanityCommand extends Command
{
    protected static $defaultName = 'grase:settings-validate';

    /** @var SettingsSanity */
    private $settingsSanity;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(SettingsSanity $settingsSanity, TranslatorInterface $translator)
    {
        parent::__construct();
        $this->settingsSanity = $settingsSanity;
        $this->translator = $translator;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $changedSettings = $this->settingsSanity->sanityCheckSettings();
        $output->writeln($this->translator->trans("grase.command.settingsSanity.updatedSettings", ['updatedSettings' => $changedSettings]));
    }
}
