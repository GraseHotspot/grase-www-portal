<?php

namespace App\Menu;

use App\Entity\Radius\Group;
use Doctrine\ORM\EntityManagerInterface;
use Pd\MenuBundle\Builder\ItemInterface;
use Pd\MenuBundle\Builder\Menu;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MainMenu extends Menu
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    /**
     * Override
     */
    public function createMenu(array $options = []): ItemInterface
    {
        // Create Root Item
        $menu = $this
            ->createRoot('settings_menu', true)// Create event is "settings_menu.event"
            ->setListAttr([])
            ->setChildAttr(['data-parent' => 'grase_radmin_homepage', 'class' => 'nav nav-pills flex-column']); // Add Parent Menu to Html Tag

        // Create Menu Items



        $menu->addChild('nav_config_groups', 5)
            ->setLabel('Groups')
            ->setRoute('grase_groups')
            ->setListAttr(['class' => 'nav-item'])
            ->setLinkAttr(['class' => 'nav-link']);
        //->setRoles(['ADMIN_SETTINGS_CONTACT'])


        $usersMenu = $menu->addChild('nav_config_users', 10)
            ->setLabel('Users')
            ->setRoute('grase_users')
            ->setListAttr(['class' => 'nav-item'])
            ->setLinkAttr(['class' => 'nav-link'])//->setRoles(['ADMIN_SETTINGS_EMAIL'])
        ;
        $this->buildUserGroupsItems($usersMenu);


        $menu->addChild('nav_report_dhcp_leases', 15)
            ->setLabel('DHCP Leases')
            ->setRoute('grase_dhcp_leases')
            ->setListAttr(['class' => 'nav-item'])
            ->setLinkAttr(['class' => 'nav-link']);
        //->setRoles(['ADMIN_SETTINGS_CONTACT'])



        $settingsMenu = $menu->addChild('nav_config_settings', 30)
            ->setLabel('Settings')
            //->setRoute('grase_settings')
            ->setListAttr(['class' => 'nav-item'])
            ->setLinkAttr(['class' => 'nav-link'])
            ->setExtra('label_icon', 'settings_application');
        //->setRoles(['ADMIN_SETTINGS_GENERAL'])


        $settingsMenu->addChild('nav_config_advanced_settings', 1)
            ->setLabel('Advanced Settings')
            ->setRoute('grase_advanced_settings')
            ->setListAttr(['class' => 'nav-item'])
            ->setLinkAttr(['class' => 'nav-link'])
            ->setExtra('label_icon', 'settings_application');
        //->setRoles(['ADMIN_SETTINGS_GENERAL'])


        return $menu;
    }

    private function buildUserGroupsItems(ItemInterface $usersMenu)
    {
        $groupRepo = $this->entityManager->getRepository(Group::class);
        $groups = $groupRepo->findAll();
        /** @var Group $group */
        foreach ($groups as $group) {

            if (!$group->getUsergroups()->isEmpty()) {
                $usersMenu->addChild('nav_config_users_' . $group->getId())
                    ->setLabel($group->getName())
                    ->setRoute('grase_users', ['group' => $group->getName()])
                    ->setListAttr(['class' => 'nav-item'])
                    ->setLinkAttr(['class' => 'nav-link']);
            }
        }

        return $usersMenu;
    }
}