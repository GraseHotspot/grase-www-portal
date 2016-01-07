<?php

namespace Grase\RadminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name = "random")
    {
        //$check_repo = $this->getDoctrine()->getManager()->getRepository('Grase\RadminBundle\Entity\Radius\Check');
        //dump($check_repo->findAll()[0]);

        //$users_repo = $this->getDoctrine()->getManager()->getRepository('Grase\RadminBundle\Entity\Radius\User');
        //dump($users_repo->findAll()[3]->getPasswordCheck());

        return $this->render('GraseRadminBundle:Default:index.html.twig', array('name' => $name));
    }

    public function displayUsersAction($group = null) {
        $users_repo = $this->getDoctrine()->getManager()->getRepository('Grase\RadminBundle\Entity\Radius\User');
        //dump($users_repo->findAll()[3]->getPasswordCheck());

        return $this->render('GraseRadminBundle:Default:users.html.twig', array('users' => $users_repo->findAll()));
    }
}
