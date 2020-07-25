<?php

namespace App\Controller;

use App\Entity\AuditLog;
use App\Entity\Radius\Group;
use App\Entity\Radius\Radacct;
use App\Entity\Radius\User;
use App\Entity\Setting;
use App\Form\SettingType;
use App\Util\GraseUtil;
use App\Util\SystemInformation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DefaultController
 * Default controller. Should be basically empty as most things will have their own controller to keep things contained
 */
class DefaultController extends AbstractController
{
    /** @var TranslatorInterface */
    private $translator;

    /**
     * DefaultController constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

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
                'systemInfo'     => $systemInformation,
                'netdataEnabled' => false, // TODO make this pull from the database settings
            ]
        );
    }



    /**
     * Display currently active Radius sessions
     *
     * @return Response
     */
    public function monitorSessionAction()
    {
        $activeSessions = $this->getDoctrine()->getRepository(Radacct::class)->findAllActiveSessions();
        //$activeSessions = $this->getDoctrine()->getRepository(Radacct::class)->findBy([], null, 20);

        return $this->render(
            'monitorSessions.html.twig',
            [
                'activeSessions' => $activeSessions,
            ]
        );
    }

    /**
     * Logs out a Chilli session
     * This function can only work on a local Coova Chilli node due to the exec used in
     * Util::getChilliLeases()
     *
     * @param Request $request
     * @param Session $session
     *
     * @return RedirectResponse
     */
    public function logoutChilliSessionAction(Request $request, Session $session)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $mac = $request->request->get('mac');
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('logout-chilli-session', $submittedToken)) {
            $session->getFlashBag()->add(
                'danger',
                $this->translator->trans('grase.error.invalid-csrf')
            );

            return $this->redirectToRoute('grase_sessions');
        }

        if (GraseUtil::logoutChilliSession($mac)) {
            $session->getFlashBag()->add('success', $this->translator->trans(
                'grase.session.logout.mac.success',
                ['mac' => $mac]
            ));
        } else {
            $session->getFlashBag()->add(
                'danger',
                $this->translator->trans(
                    'grase.session.logout.mac.failed',
                    ['mac' => $mac]
                )
            );
        }

        return $this->redirectToRoute('grase_sessions');
    }

    /**
     * Display all the DHCP leases from Coova Chilli. This function can only work on a local Coova Chilli node due to
     * the exec used in Util::getChilliLeases()
     *
     * @param Session $session
     *
     * @return Response
     */
    public function dhcpLeasesAction(Session $session)
    {
        $chilliLeases = GraseUtil::getChilliLeases();
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
     *
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

    /**
     * Global search of users/groups (and eventually more?)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function searchAction(Request $request)
    {
        // @TODO ensure we can only search for things we already have access to
        $search = $request->request->get('search');
        if (empty($search)) {
            throw $this->createNotFoundException('Empty search');
        }

        $matchingUsers = $this->getDoctrine()->getRepository(User::class)->searchByUsername($search);
        $matchingGroups = $this->getDoctrine()->getRepository(Group::class)->searchByGroupname($search);

        return $this->render(
            'searchResults.html.twig',
            [
                'users'      => $matchingUsers,
                'groups'     => $matchingGroups,
                'searchTerm' => $search,
            ]
        );
    }
}
