<?php

namespace App\Menu;

use Pd\MenuBundle\Builder\Item;

class DefaultItem extends Item
{
    protected $childAttr = ['class' => 'nav'];
    protected $listAttr = ['class' => 'nav-item'];
    protected $linkAttr = ['class' => 'nav-link'];
}
