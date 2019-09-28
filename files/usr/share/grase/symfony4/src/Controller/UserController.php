<?php

namespace App\Controller;

use App\Entity\Radius\User;
use App\Entity\Radius\UserRepository;
use App\Entity\UpdateUserData;
use App\Form\Radius\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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


    public function editUserAction(Request $request, $id)
    {
        /** @var User $user */
        $user = $this->getDoctrine()
                     ->getRepository(User::class)
                     ->find($id);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        // @TODO Insert permissions check here for editing

        $updateUserData = UpdateUserData::fromUser($user);

        $form = $this->createForm(UserType::class, $updateUserData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $updateUserData->updateUser($user, $this->getDoctrine()->getManager());
            $this->addFlash(
                'success',
                $this->translator->trans(
                    'grase.user.save_success.%username%',
                    ['%username%' => $user->getUsername()]
                )
            );

            return $this->redirectToRoute('grase_user_edit', ['id' => $user->getUsername()]);
        }

        return $this->render(
            'user_edit.html.twig',
            [
                'user' => $user,
                'user_form' => $form->createView(),
            ]
        );
    }

    public function createUserAction(Request $request)
    {
        // @TODO Insert permissions check here for creating
        /** @var User $user */
        $user = new User();

        $newUserData = new UpdateUserData();

        $form = $this->createForm(UserType::class, $newUserData, ['create' => true]);

        $form->handleRequest($request);

        dump($newUserData);
        if ($form->isSubmitted() && $form->isValid()) {
            // It's a new user, we need to set the username, we don't do this for editing though.
            $user->setUsername($newUserData->username);
            $newUserData->updateUser($user, $this->getDoctrine()->getManager());
            $this->addFlash(
                'success',
                $this->translator->trans(
                    'grase.user.save_success.%username%',
                    ['%username%' => $user->getUsername()]
                )
            );

            return $this->redirectToRoute('grase_user_edit', ['id' => $user->getUsername()]);

        }

        return $this->render(
            'user_edit.html.twig',
            [
                'user' => $user,
                'user_form' => $form->createView(),
            ]
        );
    }

}
