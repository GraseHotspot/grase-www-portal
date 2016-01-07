<?php
namespace Grase\RadminBundle\EventListener;

use Avanzu\AdminThemeBundle\Model\MenuItemModel;
use Avanzu\AdminThemeBundle\Event\SidebarMenuEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class MenuItemListListener
{

    private $securityChecker;

    public function __construct(AuthorizationChecker $securityChecker)
    {
        $this->securityChecker = $securityChecker;
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

            $items = [
                'grase_radmin_homepage' => 'Status',
                'grase_users' => [
                    "label" => "User Accounts",
                    // TODO Make this dynamically generated based on available groups
                    'children' => [
                        'users_computers' => [
                            'route' => 'grase_users_group',
                            'label' => 'Computer Accounts',
                            'route_args' => ['group' => 'computers']
                        ],
                        'users_staff' => [
                            'route' => 'grase_users_group',
                            'label' => 'Staff Accounts',
                            'route_args' => ['group' => 'staff']
                        ]
                    ],
                ],
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