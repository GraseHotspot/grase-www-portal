<?php

namespace App\Controller;

use App\Command\GraseFirstRunCommand;
use App\Entity\Setting;
use App\Util\SettingsUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/")
 */
class SecurityController extends AbstractController
{
    /**
     * This route displays the login form. We don't need to process it as the symfony security layer does that
     *
     * @Route("/login", name="_grase_login")
     *
     * @param Request       $request
     * @param SettingsUtils $settingsUtils
     *
     * @return Response
     */
    public function loginAction(Request $request, SettingsUtils $settingsUtils)
    {
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(Security::AUTHENTICATION_ERROR);
        }

        $firstRunWizardVersion = $settingsUtils->getSettingValue(Setting::FIRST_RUN_WIZARD_VERSION);

        return $this->render(
            'login.html.twig',
            [
                'last_username'        => $request->getSession()->get(Security::LAST_USERNAME),
                'error'                => $error,
                'firstRunWizardNeeded' => $firstRunWizardVersion < GraseFirstRunCommand::WIZARD_VERSION,
            ]
        );
    }

    /**
     * @Route("/login_check", name="_grase_security_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="_grase_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }
}
