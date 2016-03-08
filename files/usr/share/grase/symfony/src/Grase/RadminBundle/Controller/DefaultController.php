<?php

namespace Grase\RadminBundle\Controller;

use Grase\RadminBundle\Entity\Radius\Group;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Grase\RadminBundle\Form\Radius\GroupType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($name = "random")
    {
        //$check_repo = $this->getDoctrine()->getManager()->getRepository('Grase\RadminBundle\Entity\Radius\Check');
        //dump($check_repo->findAll()[0]);

        $users_repo = $this->getDoctrine()->getManager()->getRepository('Grase\RadminBundle\Entity\Radius\User');
        dump($users_repo->findByUsername('f2ed1e351b48')[0]->getRadiusAccounting());

        return $this->render('GraseRadminBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * @Route("/users/{group}", name="grase_users")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayUsersAction($group = null)
    {
        $users_repo = $this->getDoctrine()->getManager()->getRepository('Grase\RadminBundle\Entity\Radius\User');

        $users = $users_repo->findByGroup($group);

        return $this->render(
            'GraseRadminBundle:Default:users.html.twig',
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
        $groups_repo = $this->getDoctrine()->getManager()->getRepository('GraseRadminBundle:Radius\Group');

        $groups = $groups_repo->findAll();

        return $this->render(
            'GraseRadminBundle:Default:groups.html.twig',
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
