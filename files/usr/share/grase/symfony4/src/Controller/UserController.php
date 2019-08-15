<?php


namespace App\Controller;


use App\Entity\Radius\User;
use App\Entity\Radius\UserRepository;
use App\Entity\UpdateUserData;
use App\Form\Radius\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;


class UserController extends Controller
{
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
            $this->addFlash('success', $this->get('translator')->trans('grase.user.save_success.%username%', ['%username%' => $user->getUsername()]));
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