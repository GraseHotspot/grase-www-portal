<?php

namespace App\Controller;

use App\Entity\Radius\Group;
use App\Entity\Radius\User;
use App\Entity\Radius\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Form\Radius\GroupType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($name = "random")
    {
        //$check_repo = $this->getDoctrine()->getManager()->getRepository('App\Entity\Radius\Check');
        //dump($check_repo->findAll()[0]);

        /** @var UserRepository $users_repo */
        $users_repo = $this->getDoctrine()->getManager()->getRepository(User::class);
        dump($users_repo->findByUsername('de5f1e4aa447')[0]->getRadiusAccounting());

        return $this->render('index.html.twig', array('name' => $name));
    }

    /**
     * @Route("/users/{group}", name="grase_users")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayUsersAction($group = null)
    {

        /** @var UserRepository $users_repo */
        $users_repo = $this->getDoctrine()->getManager()->getRepository(User::class);

        $users = $users_repo->findByGroup($group);

        return $this->render(
            'users.html.twig',
            [
                'users' => $users
            ]
        );
    }

    /**
     * @Route("/groups/", name="grase_groups")
     */
    public function displayGroups()
    {
        $groups_repo = $this->getDoctrine()->getManager()->getRepository(Group::class);

        $groups = $groups_repo->findAll();

        return $this->render(
            'groups.html.twig',
            [
                'groups' => $groups
            ]
        );
    }

    /**
     * @Route("/group/{id}/edit", name="grase_group_edit")
     */
    public function editGroup(Request $request, Group $group)
    {

        // Insert permissions check here for editing

        $form = $this->createForm(GroupType::class, $group);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->container->get('grase.manager.group')->saveGroup($group);

            $this->addFlash('success', $this->get('translator')->trans('grase.group.save_success.%groupname%', ['%groupname%' => $group->getName()]));

            return $this->redirectToRoute('grase_groups');
        }

        return $this->render(
            'GraseRadminBundle:Default:group_edit.html.twig',
            [
                'group' => $group,
                'group_form' => $form->createView()
            ]
        );
    }
}
