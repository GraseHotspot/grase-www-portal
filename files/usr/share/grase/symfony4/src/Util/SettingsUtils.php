<?php


namespace App\Util;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SettingsUtils
 * Utilities for settings. Makes it easier to get some settings out in a useable format
 */
class SettingsUtils
{
    /** @var SettingRepository */
    private $settingRepository;

    /** @var Formatting */
    private $formatting;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * SettingsUtils constructor.
     *
     * @param SettingRepository   $settingRepository
     * @param Formatting          $formatting
     * @param TranslatorInterface $translator
     */
    public function __construct(SettingRepository $settingRepository, Formatting $formatting, TranslatorInterface $translator)
    {
        $this->settingRepository = $settingRepository;
        $this->formatting        = $formatting;
        $this->translator        = $translator;
    }

    /**
     * Fetch the settings value, replaces the Radmin->getSetting() function
     * @param string $settingName
     *
     * @return string|null
     */
    public function getSettingValue($settingName)
    {
        /** @var Setting $setting */
        $setting = $this->settingRepository->find($settingName);
        if ($setting) {
            return $setting->getValue();
        }

        return null;
    }

    /**
     * Returns an array for use as a dropdown of Bytes options nicely formatted
     * @return array
     */
    public function mbOptionsArray()
    {
        $mbOptions = json_decode($this->settingRepository->find(Setting::MB_OPTIONS)->getValue());
        //array_map(function ($option) { return })
        $options = [];
        foreach ($mbOptions as $mb) {
            $bytes                                           = $mb * 1024 * 1024;
            $options[$this->formatting->formatBytes($bytes)] = $bytes;
        }

        return $options;
    }

    /**
     * Returns an array for use as a dropdown of Time options nicely formatted
     * @return array
     */
    public function timeOptionsArray()
    {
        $timeOptions = json_decode($this->settingRepository->find(Setting::TIME_OPTIONS)->getValue());
        $options     = [];
        foreach ($timeOptions as $time) {
            if ($time >= 60) {
                $label = $this->translator->trans('time.hours', ['hours' => $time / 60]);
            } else {
                $label = $this->translator->trans('time.minutes', ['minutes' => $time]);
            }
            // We need the value to be seconds, not minutes
            $options[$label] = $time * 60;
        }

        return $options;
    }
}
