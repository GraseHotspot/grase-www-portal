<?php

namespace App\Util;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;

/**
 * Class SettingsSanity
 *
 * Perform some sanity checks on the settings
 */
class SettingsSanity
{
    /** @var SettingRepository */
    private $settingsRepository;

    /** @var EntityManagerInterface */
    private $em;

    /** @var Logger */
    private $auditLogger;

    /** @var SettingsUtils */
    private $settingsUtils;

    /**
     * Defaults for numeric settings
     * @var array
     */
    private $numericDefaults = [
        Setting::PASSWORD_LENGTH => 6,
        Setting::USERNAME_LENGTH => 5,
    ];

    /**
     * Defaults for string settings
     * @var array
     */
    private $stringDefaults = [
        'locationName'       => 'Default',
        Setting::SUPPORT_CONTACT_NAME => 'Tim White',
        Setting::SUPPORT_CONTACT_LINK => 'https://grasehotspot.com/',
        Setting::WEBSITE_LINK        => 'https://grasehotspot.org/',
        Setting::WEBSITE_NAME        => 'GRASE Hotspot Project',
        'locale'             => 'en_AU',
    ];

    /**
     * Array of old default setting values so we can upgrade existing installs
     * @var array
     */
    private $oldStringDefaults = [
        Setting::SUPPORT_CONTACT_LINK => ['http://grasehotspot.com/', 'http://grasehotspot.org/', 'http://grasehotspot.org', 'http://grasehotspot.com'],
        'websiteLink'        => ['http://grasehotspot.org/', 'http://grasehotspot.org'],
    ];

    /**
     * Defaults for arrays of settings
     * @var array
     */
    private $arrayDefaults = [
        'mbOptions'   => [
            10,
            50,
            100,
            250,
            500,
            1024,
            2048,
            4096,
            10240,
            102400,
        ],
        'timeOptions' => [
            5,
            10,
            20,
            30,
            45,
            60,
            90,
            120,
            180,
            240,
            600,
            6000,
        ],
        'kBitOptions' => [
            64,
            128,
            256,
            512,
            1024,
            1536,
            2048,
            4096,
            8192,
        ],
    ];

    /**
     * SettingsSanity constructor.
     *
     * @param SettingRepository      $settingsRepository
     * @param EntityManagerInterface $em
     * @param Logger                 $auditLogger
     * @param SettingsUtils          $settingsUtils
     */
    public function __construct(SettingRepository $settingsRepository, EntityManagerInterface $em, Logger $auditLogger, SettingsUtils $settingsUtils)
    {
        $this->settingsRepository = $settingsRepository;
        $this->em                 = $em;
        $this->auditLogger        = $auditLogger;
        $this->settingsUtils = $settingsUtils;
    }

    /**
     * Run a sanity check on all settings, remove old settings, fixup invalid settings
     * @return array
     */
    public function sanityCheckSettings()
    {
        // Fetch all settings so they are preloaded
        $allSettings = $this->settingsRepository->findAll();
        $removedSettingsCount = $this->removeOldSettings($allSettings);

        $this->settingsUtils->convertSettingsJson();

        $this->defaultInvalidSettings();
        $this->upgradeDefaultSettings();

        return [
            'changedSettings' => $this->settingsUtils->flushChangedSettings(),
            'removedSettings' => $removedSettingsCount,
        ];
    }

    /**
     * Remove all settings that are no longer used by Grase Hotspot
     *
     * @param array $allSettings Array of all the settings objects
     *
     * @return int Number of removed settings
     */
    private function removeOldSettings($allSettings)
    {
        $removedSettingsCount = 0;

        $oldSettings = [
            'priceMB',
            'priceMinute',
            'currency',
            'sellableData',
            'userableData',
            'groups',
        ];

        $existingSettings = array_map(
            function (Setting $setting) {
                return $setting->getName();
            },
            $allSettings
        );

        foreach ($oldSettings as $setting) {
            if (in_array($setting, $existingSettings)) {
                $oldSetting = $this->settingsRepository->find($setting);
                $this->em->remove($oldSetting);
                $removedSettingsCount++;
                $this->auditLogger->info('settings.sanity.removed_setting', ['setting' => $setting]);
            };
        }

        if ($removedSettingsCount) {
            $this->em->flush();
        }

        return $removedSettingsCount;
    }

    /**
     * Gets a setting, and if it doesn't exist, create it
     *
     * @param string $name         Name of setting to find or create
     * @param string $defaultValue Default value so we can create the setting if it's missing
     *
     * @return Setting
     */
    private function getSetting($name, $defaultValue)
    {
        $setting = $this->settingsRepository->find($name);
        if (!$setting) {
            $setting = new Setting($name);
            $this->settingsUtils->updateSetting($setting, $defaultValue);
            $this->auditLogger->info('settings.sanity.created_missing_setting', ['setting' => $name]);
        }

        return $setting;
    }

    /**
     * Upgrade an old default setting to a new default setting
     */
    private function upgradeDefaultSettings()
    {
        foreach ($this->oldStringDefaults as $settingName => $oldDefaults) {
            $setting = $this->getSetting($settingName, $this->stringDefaults[$settingName]);
            if (in_array($setting->getValue(), $oldDefaults)) {
                $this->settingsUtils->updateSetting($setting, $this->stringDefaults[$settingName]);
            }
        }
    }

    /**
     * Resets settings to defaults if they aren't valid
     *
     * @return int
     */
    private function defaultInvalidSettings()
    {
        foreach ($this->numericDefaults as $settingName => $default) {
            $setting = $this->getSetting($settingName, $default);
            if (!is_numeric($setting->getValue()) || $setting->getValue() < 1) {
                $this->settingsUtils->updateSetting($setting, $default);
            }
        }

        foreach ($this->stringDefaults as $settingName => $default) {
            $setting = $this->getSetting($settingName, $default);
            if (empty($setting->getValue())) {
                $this->settingsUtils->updateSetting($setting, $default);
            }
        }

        foreach ($this->arrayDefaults as $settingName => $default) {
            $setting = $this->getSetting($settingName, $default);
            // Check if null
            if (empty($setting->getValue())) {
                $this->settingsUtils->updateSetting($setting, $default);
            }
        }
    }
}
