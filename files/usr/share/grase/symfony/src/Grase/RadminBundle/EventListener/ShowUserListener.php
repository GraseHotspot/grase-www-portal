<?php
namespace Grase\RadminBundle\EventListener;

use Avanzu\AdminThemeBundle\Event\ShowUserEvent;
use Grase\RadminBundle\Entity\Radmin\User;


class ShowUserListener {

    protected $session, $doctrine, $container;

    public function __construct($session, $doctrine, $service_container)
    {
        $this->session = $session;
        $this->doctrine = $doctrine;
        $this->container = $service_container;
    }

    public function onShowUser(ShowUserEvent $event) {

        $user = $this->getUser();
        $event->setUser($user);

    }

    protected function getUser() {
        // retrieve your concrete user model or entity
        return $this->container->get('security.token_storage')->getToken()->getUser();
    }

}