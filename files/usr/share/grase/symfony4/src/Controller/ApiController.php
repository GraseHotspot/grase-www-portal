<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Util\SettingsUtils;
use Grase\Util;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/internal/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/generate/password", options={"expose"=true}, name="grase_api_generate_password")
     *
     * @param SettingsUtils $settingsUtils
     *
     * @return JsonResponse
     */
    public function apiGeneratePassword(SettingsUtils $settingsUtils)
    {
        return new JsonResponse(Util::randomPassword($settingsUtils->getSettingValue(Setting::PASSWORD_LENGTH)));
    }
}
