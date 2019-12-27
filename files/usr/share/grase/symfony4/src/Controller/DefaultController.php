<?php

namespace App\Controller;

use App\Entity\AuditLog;
use App\Entity\Radius\User;
use App\Entity\Setting;
use App\Util\SettingsUtils;
use Grase\SystemInformation;
use Grase\Util;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class DefaultController
 * Default controller. Should be basically empty as most things will have their own controller to keep things contained
 */
class DefaultController extends AbstractController
{
    /**
     * Our "System Information" dashboard. This is the landing page after logging in.
     *
     * @return Response
     */
    public function indexAction()
    {
        $systemInformation = new SystemInformation();

        return $this->render(
            'index.html.twig',
            [
                'systemInfo' => $systemInformation,
                'netdataEnabled' => false, // TODO make this pull from the database settings
            ]
        );
    }


    /**
     * Show all the settings in a table so we can see the "hidden" settings.
     * TODO this will allow editing of any setting
     * @return Response
     */
    public function advancedSettingsAction()
    {
        $settings = $this->getDoctrine()
                         ->getRepository(Setting::class)
                         ->findAll();

        return $this->render(
            'advancedSettings.html.twig',
            [
                'settings' => $settings,
            ]
        );
    }

    /**
     * Display all the DHCP leases from Coova Chilli. This function can only work on a local Coova Chilli node due to
     * the exec used in Util::getChilliLeases()
     * @param Session $session
     *
     * @return Response
     */
    public function dhcpLeasesAction(Session $session)
    {
        $chilliLeases = Util::getChilliLeases();
        if ($chilliLeases && isset($chilliLeases['sessions'])) {
            $sessions = $chilliLeases['sessions'];
        } else {
            $session->getFlashBag()->add(
                'danger',
                'Unable to fetch DHCP leases for Coova Chilli'
            );
            $sessions = [];
        }

        // Prewarm all users with a single query so we can get the comments as required
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        foreach ($sessions as $id => $session) {
            if (isset($session['session']) && isset($session['session']['userName'])) {
                /** @var User $user */
                $user = $this->getDoctrine()->getRepository(User::class)->find($session['session']['userName']);
                if ($user) {
                    $sessions[$id]['session']['comment'] = $user->getComment();
                }
            }
        }
        // Make sure we "use" $users so it's not an unused variable
        unset($users);

        return $this->render(
            'dhcpLeases.html.twig',
            [
                'sessions' => $sessions,
            ]
        );
    }

    /**
     * Display a table of audit logs
     * @return Response
     */
    public function auditLogDisplayAction()
    {
        $auditLogRepo = $this->getDoctrine()->getManager()->getRepository(AuditLog::class);

        $logEntries = $auditLogRepo->findBy([], ['createdAt' => 'DESC'], 500);

        return $this->render(
            'auditlog.html.twig',
            [
                'logEntries' => $logEntries,
            ]
        );
    }
}
