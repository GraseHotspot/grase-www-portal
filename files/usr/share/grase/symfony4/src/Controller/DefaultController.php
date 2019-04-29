<?php

namespace App\Controller;

use App\Entity\Radius\Group;
use App\Entity\Radius\User;
use App\Entity\Radius\UserRepository;
use App\Entity\Setting;
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
                'systemInfo' => $systemInformation
            ]
        );
    }

    /**
     * @return Response
     */
    public function displayUsersAction($group = null)
    {

        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getManager()->getRepository(User::class);

        $users = $userRepository->findByGroup($group);

        return $this->render(
            'users.html.twig',
            [
                'users' => $users,
            ]
        );
    }

    public function displayGroupsAction()
    {
        $groupsRepository = $this->getDoctrine()->getManager()->getRepository(Group::class);

        $groups = $groupsRepository->findAll();

        return $this->render(
            'groups.html.twig',
            [
                'groups' => $groups,
            ]
        );
    }

    public function editGroupAction(Request $request, $id)
    {
        /** @var Group $group */
        $group = $this->getDoctrine()
                     ->getRepository(Group::class)
                     ->find($id);

        if (!$group) {
            throw $this->createNotFoundException();
        }

        // @TODO Insert permissions check here for editing

        $form = $this->createForm(GroupType::class, $group);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('grase.manager.group')->saveGroup($group);

            $this->addFlash('success', $this->get('translator')->trans('grase.group.save_success.%groupname%', ['%groupname%' => $group->getName()]));

            return $this->redirectToRoute('grase_groups');
        }

        return $this->render(
            'GraseRadminBundle:Default:group_edit.html.twig',
            [
                'group' => $group,
                'group_form' => $form->createView(),
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
                'settings' => $settings
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
        foreach($sessions as $id => $session) {
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
                'sessions' => $sessions
            ]
        );

    }
}
