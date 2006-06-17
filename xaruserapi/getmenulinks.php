<?php
/**
 * @package commerce
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage carts
 * @author Marc Lutolf (mfl@netspan.ch)
*/

/**
 * Pass individual menu items to the user menu
 *
 * @return array containing the menulinks for the user menu items.
 */
function carts_userapi_getmenulinks()
{
    $menulinks[] = array('url'   => xarModURL('carts','user','main'),
                         'title' => xarML('View cart'),
                         'label' => xarML('View Cart'));

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>