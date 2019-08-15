<?php


namespace App\Controller;


use App\Entity\Radius\Group;
use App\Form\Radius\GroupType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;


class GroupController extends Controller
{
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
            'group_edit.html.twig',
            [
                'group' => $group,
                'group_form' => $form->createView(),
            ]
        );
    }

}