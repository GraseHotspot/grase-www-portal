<?php

namespace Grase\RadminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name = "random")
    {
        return $this->render('GraseRadminBundle:Default:index.html.twig', array('name' => $name));
    }
}
