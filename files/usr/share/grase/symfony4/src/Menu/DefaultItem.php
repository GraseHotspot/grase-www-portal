<?php

namespace App\Menu;

use Pd\MenuBundle\Builder\Item;

/**
 * Class DefaultItem
 *
 * This class is an easy way to have defaults for all our items, use this instead of 'new Item()'
 */
class DefaultItem extends Item
{
    protected $childAttr = ['class' => 'nav'];
    protected $listAttr = ['class' => 'nav-item'];
    protected $linkAttr = ['class' => 'nav-link'];
}
