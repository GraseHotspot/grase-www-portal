<?php

namespace App\Controller;

use App\Entity\Radius\Group;
use App\Entity\Radius\GroupManager;
use App\Form\Radius\GroupType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class GroupController
 * Routes for all Group related actions
 */
class GroupController extends AbstractController
{
    /** @var TranslatorInterface */
    protected $translator;

    /**
     * GroupController constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Display all groups
     * @return Response
     */
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


    /**
     * Edit Group settings
     * @param Request      $request
     * @param GroupManager $groupManager
     * @param string       $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editGroupAction(Request $request, GroupManager $groupManager, $id)
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
            $groupManager->saveGroup($group);

            $this->addFlash(
                'success',
                $this->translator->trans(
                    'grase.group.save_success.%groupname%',
                    ['%groupname%' => $group->getName()]
                )
            );

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
