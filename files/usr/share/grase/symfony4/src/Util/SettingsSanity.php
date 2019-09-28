<?php


namespace App\Util;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

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

    /** @var LoggerInterface */
    private $logger;

    public function __construct(SettingRepository $settingsRepository, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->settingsRepository = $settingsRepository;
        $this->em = $em;
        $this->logger = $logger;
    }

    public function sanityCheckSettings()
    {
        // Fetch all settings so the are preloaded
        $allSettings = $this->settingsRepository->findAll();

        $this->removeOldSettings();
        $this->defaultInvalidSettings();

        if ($this->changedSettings) {
            $this->em->flush();
        }

        return $this->changedSettings;
    }

    /**
     * Remove all settings that are no longer used by Grase Hotspot
     *
     * @return int Number of removed settings
     */
    private function removeOldSettings()
    {
        $oldSettings = [
            'priceMB',
            'priceMinute',
            'currency',
            'sellableData',
            'userableData',
            'groups',
            ];

        foreach ($oldSettings as $setting) {
            $oldSetting = $this->settingsRepository->find($setting);
            if ($oldSetting) {
                $this->em->remove($oldSetting);
                $this->changedSettings++;
                $this->logger->info("Removing setting $setting");
            };
        }
    }

    /**
     * Gets a setting, and if it doesn't exist, create it
     * @param string $name Name of setting to find or create
     */
    private function getSetting($name, $defaultValue)
    {
        $setting = $this->settingsRepository->find($name);
        if (!$setting) {
            $setting = new Setting($name);
            $setting->setValue($defaultValue);
            $this->em->persist($setting);
            $this->changedSettings++;
            $this->logger->info("Creating missing setting $name");
        }

        return $setting;
    }

    private function updateSetting(Setting $setting, $value)
    {
        $setting->setValue($value);
        $this->em->persist($setting);
        $this->changedSettings++;
        $this->logger->info("Updated setting ". $setting->getName(), ['setting' => $setting, 'value' => $value]);
    }

    /**
     * Resets settings to defaults if they aren't valid
     *
     * @return int
     */
    private function defaultInvalidSettings()
    {
        $numericDefaults = [
            'passwordLength' => 6,
            'usernameLength' => 5,
        ];

        $stringDefaults = [
            'locationName' => 'Default',
            'supportContactName' => 'Tim White',
            'supportContactLink' => 'https://grasehotspot.com/',
            'websiteLink' => 'https://grasehotspot.org/',
            'websiteName' => 'GRASE Hotspot Project',
            'locale' => 'en_AU',
        ];

        $arrayDefaults = [
            'mbOptions' => [
                10, 50, 100, 250, 500, 1024, 2048, 4096, 10240, 102400,
            ],
            'timeOptions' => [
                5, 10, 20, 30, 45, 60, 90, 120, 180, 240, 600, 6000,
            ],
            'kBitOptions' => [
                64, 128, 256, 512, 1024, 1536, 2048, 4096, 8192,
            ],
        ];

        foreach ($numericDefaults as $settingName => $default) {
            $setting = $this->getSetting($settingName, $default);
            if (!is_numeric($setting->getValue()) || $setting->getValue() < 1) {
                $this->updateSetting($setting, $default);
            }
        }

        foreach ($stringDefaults as $settingName => $default) {
            $setting = $this->getSetting($settingName, $default);
            if (empty($setting->getValue())) {
                $this->updateSetting($setting, $default);
            }
        }

        foreach ($arrayDefaults as $settingName => $default) {
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