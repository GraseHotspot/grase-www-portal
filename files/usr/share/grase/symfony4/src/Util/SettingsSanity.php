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

    /** @var int */
    private $changedSettings = 0;

    /** @var Logger */
    private $auditLogger;

    /**
     * Defaults for numeric settings
     * @var array
     */
    private $numericDefaults = [
        'passwordLength' => 6,
        'usernameLength' => 5,
    ];

    /**
     * Defaults for string settings
     * @var array
     */
    private $stringDefaults = [
        'locationName'       => 'Default',
        'supportContactName' => 'Tim White',
        'supportContactLink' => 'https://grasehotspot.com/',
        'websiteLink'        => 'https://grasehotspot.org/',
        'websiteName'        => 'GRASE Hotspot Project',
        'locale'             => 'en_AU',
    ];

    /**
     * Array of old default setting values so we can upgrade existing installs
     * @var array
     */
    private $oldStringDefaults = [
        'supportContactLink' => ['http://grasehotspot.com/', 'http://grasehotspot.org/', 'http://grasehotspot.org', 'http://grasehotspot.com'],
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
     */
    public function __construct(SettingRepository $settingsRepository, EntityManagerInterface $em, Logger $auditLogger)
    {
        $this->settingsRepository = $settingsRepository;
        $this->em                 = $em;
        $this->auditLogger        = $auditLogger;
    }

    /**
     * Run a sanity check on all settings, remove old settings, fixup invalid settings
     * @return int
     */
    public function sanityCheckSettings()
    {
        // Fetch all settings so they are preloaded
        $allSettings = $this->settingsRepository->findAll();

        $this->removeOldSettings($allSettings);
        $this->defaultInvalidSettings();
        $this->upgradeDefaultSettings();

        if ($this->changedSettings) {
            $this->em->flush();
        }

        return $this->changedSettings;
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
                $this->changedSettings++;
                $this->auditLogger->info('settings.sanity.removed_setting', ['setting' => $setting]);
            };
        }
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
            $setting->setValue($defaultValue);
            $this->em->persist($setting);
            $this->changedSettings++;
            $this->auditLogger->info('settings.sanity.created_missing_setting', ['setting' => $name]);
        }

        return $setting;
    }

    /**
     * @param Setting $setting
     * @param string  $value
     *
     * Actually do the updating of a setting
     */
    private function updateSetting(Setting $setting, $value)
    {
        $setting->setValue($value);
        $this->em->persist($setting);
        $this->changedSettings++;
        $this->auditLogger->info(
            'settings.sanity.updated_setting',
            ['setting' => $setting->getName(), 'value' => $value]
        );
    }

    /**
     * Upgrade an old default setting to a new default setting
     */
    private function upgradeDefaultSettings()
    {
        foreach ($this->oldStringDefaults as $settingName => $oldDefaults) {
            $setting = $this->getSetting($settingName, $this->stringDefaults[$settingName]);
            if (in_array($setting->getValue(), $oldDefaults)) {
                $this->updateSetting($setting, $this->stringDefaults[$settingName]);
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
                $this->updateSetting($setting, $default);
            }
        }

        foreach ($this->stringDefaults as $settingName => $default) {
            $setting = $this->getSetting($settingName, $default);
            if (empty($setting->getValue())) {
                $this->updateSetting($setting, $default);
            }
        }

        foreach ($this->arrayDefaults as $settingName => $default) {
            $setting = $this->getSetting($settingName, json_encode($default));
            // Check if null
            if (empty($setting->getValue())) {
                $this->updateSetting($setting, json_encode($default));
                continue;
            }

            // Try JSON
            $settingArray = json_decode($setting->getValue());
            if (json_last_error() === JSON_ERROR_NONE) {
                // We have a valid json object, check it's an array
                if (!is_array($settingArray) || empty($settingArray)) {
                    $this->updateSetting($setting, json_encode($default));
                }
                continue;
            }

            // Try space delimited string
            $settingArray = explode(" ", $setting->getValue());
            if (is_array($settingArray) && !empty($settingArray)) {
                // We have an array, so lets convert it to json
                $settingArray = array_map('intval', $settingArray);
                $this->updateSetting($setting, json_encode($settingArray));
                continue;
            }

            // Just set to default, it's certainly not valid!
            $this->updateSetting($setting, json_encode($default));
        }
    }
}
