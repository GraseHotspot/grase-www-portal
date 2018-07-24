<?php
namespace App\EventListener;

use Avanzu\AdminThemeBundle\Model\MenuItemModel;
use Avanzu\AdminThemeBundle\Event\SidebarMenuEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use App\Entity\Radius\Group;

class MenuItemListListener
{

    private $securityChecker;
    private $doctrine;

    public function __construct(AuthorizationChecker $securityChecker, Registry $doctrine)
    {
        $this->securityChecker = $securityChecker;
        $this->doctrine = $doctrine;
    }

    public function onSetupMenu(SidebarMenuEvent $event)
    {
        $request = $event->getRequest();

        foreach ($this->getMenu($request) as $item) {
            $event->addItem($item);
        }
    }

    protected function getMenu(Request $request)
    {
        // retrieve your menuItem models/entities here
        $items = [];
        if ($this->securityChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            // build user accounts / groups lists
            $user_groups=['grase_users' => [
                'label' => 'All',
            ]];
            $groups = $this->doctrine->getRepository('GraseRadminBundle:Radius\Group')->findAll();
            /** @var Group $group */
            foreach ($groups as $group) {
                $user_groups['users_' . $group->getName()] = [
                    'route' => 'grase_users',
                    'label' => $group->getName(),
                    'route_args' => ['group' => $group->getName()]
                ];
            }

            $items = [
                'grase_radmin_homepage' => 'Status',
                'grase_users' => [
                    "label" => "User Accounts",
                    'children' => $user_groups, // Dynamically generated based on available groups
                ],
                'grase_groups' => [
                    "label" => "Groups"
                ]
            ];
        }
        $menuItems = array();
        foreach ($items as $key => $label) {
            if (is_array($label)) {
                $menuItems[] = $this->buildMenuItem($key, $label);
            } else {
                $menuItems[] = new MenuItemModel($key, $label, $key);
            }
        }
        return $this->activateByRoute($request->get('_route'), $menuItems);
    }

    protected function buildMenuItem($route, $item)
    {
        $menuitem = new MenuItemModel($route, $item['label'], $route);
        if (isset($item['route_args'])) {
            $menuitem->setRouteArgs($item['route_args']);
        }
        if (isset($item['children'])) {
            foreach ($item['children'] as $child_route => $child_item) {
                if (isset($child_item['route'])) {
                    $child_route = $child_item['route'];
                }
                $childitem = $this->buildMenuItem($child_route, $child_item);
                $menuitem->addChild($childitem);
            }
        }
        return $menuitem;
    }

    protected function activateByRoute($route, $items)
    {

        foreach ($items as $item) {
            if ($item->hasChildren()) {
                $this->activateByRoute($route, $item->getChildren());
            }
            if ($item->getRoute() == $route) {
                $item->setIsActive(true);
            }
        }

        return $items;
    }
}
