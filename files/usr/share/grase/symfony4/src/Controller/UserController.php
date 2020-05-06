<?php

namespace App\Controller;

use App\Entity\Radius\Group;
use App\Entity\Radius\User;
use App\Entity\Radius\UserRepository;
use App\Entity\Setting;
use App\Entity\UpdateUserData;
use App\Form\Radius\UserDeleteType;
use App\Form\Radius\UserResetExpiryType;
use App\Form\Radius\UserType;
use App\Util\GraseUtil;
use App\Util\SettingsUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController
 * All User related routes
 */
class UserController extends AbstractController
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var SettingsUtils */
    protected $settingsUtils;

    /**
     * UserController constructor.
     *
     * @param TranslatorInterface $translator
     * @param SettingsUtils       $settingsUtils
     */
    public function __construct(TranslatorInterface $translator, SettingsUtils $settingsUtils)
    {
        $this->translator = $translator;
        $this->settingsUtils = $settingsUtils;
    }

    /**
     * Display all users, filtered by group if a group is passed in
     *
     * @param string|null $group
     *
     * @return Response
     */
    public function displayUsersAction($group = null)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getManager()->getRepository(User::class);
        $groupRepository = $this->getDoctrine()->getRepository(Group::class);

        if ($group) {
            $groupObject = $groupRepository->findByName($group);
            if (!$groupObject) {
                throw $this->createNotFoundException('Invalid Group');
            }
        }

        $users = $userRepository->findByGroup($group);

        return $this->render(
            'users.html.twig',
            [
                'users' => $users,
            ]
        );
    }

    /**
     * Edit an existing user
     *
     * @param Request $request
     * @param string  $id      Username of the user to edit
     *
     * @return RedirectResponse|Response
     */
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

        $resetExpiryForm = $this->createForm(UserResetExpiryType::class, $updateUserData);
        $resetExpiryForm->handleRequest($request);

        if ($resetExpiryForm->isSubmitted() && $resetExpiryForm->isValid()) {
            $updateUserData->updateUser($user, $this->getDoctrine()->getManager(), false, true);
            $this->addFlash(
                'success',
                $this->translator->trans(
                    'grase.user.save_success.%username%',
                    ['%username%' => $user->getUsername()]
                )
            );

            return $this->redirectToRoute('grase_user_edit', ['id' => $user->getUsername()]);
        }

        $deleteUserForm = $this->createForm(UserDeleteType::class, $updateUserData, [
            'action' => $this->generateUrl('grase_user_delete', ['user' => $user->getUsername()]),
        ]);

        return $this->render(
            'user_edit.html.twig',
            [
                'user'                   => $user,
                'user_form'              => $form->createView(),
                'user_reset_expiry_form' => $resetExpiryForm->createView(),
                'user_delete_form'       => $deleteUserForm->createView(),
            ]
        );
    }

    /**
     * Delete a user
     *
     * @param Request $request
     * @param User    $user
     *
     * @return RedirectResponse
     */
    public function deleteUserAction(Request $request, User $user)
    {
        // @TODO Insert permissions check here for deleting

        $updateUserData = new UpdateUserData();

        $deleteUserForm = $this->createForm(UserDeleteType::class, $updateUserData);
        $deleteUserForm->handleRequest($request);

        if ($deleteUserForm->isSubmitted() && $deleteUserForm->isValid() && $updateUserData->username === $user->getUsername()) {
            if (UpdateUserData::deleteUser($user, $this->getDoctrine()->getManager())) {
                $this->addFlash(
                    'success',
                    $this->translator->trans(
                        'grase.form.user.delete.success.%username%',
                        ['%username%' => $updateUserData->username]
                    )
                );

                return $this->redirectToRoute('grase_user_new');
            }
        }

        $this->addFlash(
            'error',
            $this->translator->trans(
                'grase.form.user.delete.fail.%username%',
                ['%username%' => $user->getUsername()]
            )
        );

        return $this->redirectToRoute('grase_user_edit', ['id' => $user->getUsername()]);
    }

    /**
     * Create a new user
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createUserAction(Request $request)
    {
        // @TODO Insert permissions check here for creating
        /** @var User $user */
        $user = new User();

        $newUserData = new UpdateUserData();
        $newUserData->password = GraseUtil::randomPassword($this->settingsUtils->getSettingValue(Setting::PASSWORD_LENGTH));

        $form = $this->createForm(UserType::class, $newUserData, ['create' => true]);

        $form->handleRequest($request);

        // TODO ensure we don't try and create an existing user. See newuser.php for our existing checks

        if ($form->isSubmitted() && $form->isValid()) {
            // It's a new user, we need to set the username, we don't do this for editing though.
            $user->setUsername($newUserData->username);
            $newUserData->updateUser($user, $this->getDoctrine()->getManager(), true);
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
                'user'      => $user,
                'user_form' => $form->createView(),
            ]
        );
    }
}
