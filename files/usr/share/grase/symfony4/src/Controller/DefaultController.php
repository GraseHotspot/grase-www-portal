<?php

namespace App\Controller;

use App\Entity\AuditLog;
use App\Entity\Radius\Group;
use App\Entity\Radius\User;
use App\Entity\Radius\UserRepository;
use App\Entity\Setting;
use App\Entity\UpdateUserData;
use App\Form\Radius\UserType;
use Grase\SystemInformation;
use Grase\Util;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Form\Radius\GroupType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    public function indexAction($name = "random")
    {
        $systemInformation = new SystemInformation();

        return $this->render(
            'index.html.twig',
            [
                'systemInfo' => $systemInformation,
            ]
        );
    }


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
