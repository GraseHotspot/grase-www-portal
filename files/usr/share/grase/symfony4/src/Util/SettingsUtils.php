<?php

namespace App\Util;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SettingsUtils
 * Utilities for settings. Makes it easier to get some settings out in a useable format
 */
class SettingsUtils
{
    /** @var SettingRepository */
    private $settingsRepository;

    /** @var Formatting */
    private $formatting;

    /** @var TranslatorInterface */
    private $translator;

    /** @var EntityManagerInterface */
    private $em;

    /** @var LoggerInterface */
    private $auditLogger;

    /** @var array Array of settings that have been changed */
    private $changedSettings = [];

    /**
     * SettingsUtils constructor.
     *
     * @param SettingRepository      $settingRepository
     * @param Formatting             $formatting
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface        $auditLogger
     */
    public function __construct(SettingRepository $settingRepository, Formatting $formatting, TranslatorInterface $translator, EntityManagerInterface $entityManager, LoggerInterface $auditLogger)
    {
        $this->settingsRepository = $settingRepository;
        $this->formatting = $formatting;
        $this->translator = $translator;
        $this->em = $entityManager;
        $this->auditLogger = $auditLogger;
    }

    /**
     * Fetch the settings value, replaces the Radmin->getSetting() function
     *
     * @param string $settingName
     *
     * @return string|null
     */
    public function getSettingValue($settingName)
    {
        /** @var Setting $setting */
        $setting = $this->settingsRepository->find($settingName);
        if ($setting) {
            return $setting->getValue();
        }

        return null;
    }

    /**
     * Returns an array for use as a dropdown of Bytes options nicely formatted
     *
     * @return array
     */
    public function mbOptionsArray()
    {
        $mbOptions = $this->settingsRepository->find(Setting::MB_OPTIONS)->getValue();
        //array_map(function ($option) { return })
        $options = [];
        foreach ($mbOptions as $mb) {
            $bytes = $mb * 1024 * 1024;
            $options[$this->formatting->formatBytes($bytes)] = $bytes;
        }

        return $options;
    }

    /**
     * Returns an array for use as a dropdown of Time options nicely formatted
     *
     * @return array
     */
    public function timeOptionsArray()
    {
        $timeOptions = $this->settingsRepository->find(Setting::TIME_OPTIONS)->getValue();
        $options = [];
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

    /**
     * Flush pending changes, returns the number of settings that have been updated
     *
     * @return int
     */
    public function flushChangedSettings()
    {
        $pendingFlushCount = $this->getChangedSettingsCount();
        if ($pendingFlushCount) {
            // Flush
            $this->em->flush();
            // Reset
            $this->changedSettings = [];
        }

        return $pendingFlushCount;
    }

    /**
     * Convert existing settings to new JSON formatted values
     */
    public function convertSettingsJson()
    {
        // Fetch all settings so they are preloaded
        $allSettings = $this->settingsRepository->findAll();

        $this->convertSettingsJsonBoolean();
        $this->convertSettingsJsonString();
        $this->convertSettingsJsonArray();

        $this->em->flush();
    }

    /**
     * Actually do the updating of a setting (showing the old raw value in the audit log)
     *
     * @param Setting $setting
     * @param         $value
     */
    public function updateSetting(Setting $setting, $value)
    {
        $oldValue = $setting->getRawValue();
        $setting->setValue($value);
        $this->em->persist($setting);
        $this->changedSettings[$setting->getName()] = true;
        $this->auditLogger->info(
            'settings.util.updated_setting',
            ['setting' => $setting->getName(), 'value' => $value, 'oldValue' => $oldValue]
        );
    }

    /**
     * @param $settingName string Setting name to fetch
     *
     * @return Setting|null
     */
    public function getSetting($settingName)
    {
        return $this->settingsRepository->find($settingName);
    }

    /**
     * @param $settingName string Setting name to update
     * @param $value mixed New setting value
     *
     * @throws \Exception
     */
    public function updateSettingByName($settingName, $value)
    {
        $setting = $this->getSetting($settingName);
        if (!$setting) {
            throw new \Exception("Setting not found $settingName");
        }

        return $this->updateSetting($setting, $value);
    }

    /**
     * Return how many changed settings are waiting for a flush
     *
     * @return int
     */
    public function getChangedSettingsCount()
    {
        return count($this->changedSettings);
    }

    /**
     * Convert existing Array settings to new JSON formatted values
     */
    private function convertSettingsJsonArray()
    {
        foreach (Setting::ARRAY_SETTINGS as $arraySetting) {
            /** @var Setting $setting */
            $setting = $this->settingsRepository->find($arraySetting);
            if (mb_substr($setting->getRawValue(), 0, 1) !== '[') {
                // We don't already have a json array

                // Try space delimited string (this will always give us an array, but we only care if it's bigger than 1 element)
                $settingArray = explode(' ', $setting->getRawValue());
                if (sizeof($settingArray) > 1) {
                    // We have an array, so lets write it back which will save as JSON
                    $settingArray = array_map('intval', $settingArray);
                    $this->updateSetting($setting, $settingArray);
                    continue;
                }

                // Assuming it's invalid, lets give it an empty array (null) so it's valid, but the defaulting sanity check will fill it in at some point
                $this->updateSetting($setting, []);
            }
        }
    }

    /**
     * Convert existing String settings to new JSON formatted values
     */
    private function convertSettingsJsonString()
    {
        foreach (Setting::STRING_SETTINGS as $stringSetting) {
            /** @var Setting $setting */
            $setting = $this->settingsRepository->find($stringSetting);
            if (mb_substr($setting->getRawValue(), 0, 1) !== '"') {
                // We don't already have a json string
                $this->updateSetting($setting, $setting->getRawValue());
            }
        }
    }

    /**
     * Convert existing Boolean settings to new JSON formatted values
     */
    private function convertSettingsJsonBoolean()
    {
        foreach (Setting::BOOLEAN_SETTINGS as $boolSetting) {
            /** @var Setting $setting */
            $setting = $this->settingsRepository->find($boolSetting);
            if (in_array($setting->getRawValue(), ['TRUE', 'FALSE'])) {
                if ($setting->getRawValue() === 'TRUE') {
                    $this->updateSetting($setting, true);
                } else {
                    $this->updateSetting($setting, false);
                }
            }
        }
    }
}
