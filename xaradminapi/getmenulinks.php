<?php
/**
 * File: $Id$
 *
 * Pass admin links to the admin menu
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage module name
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


/**
 * Pass individual menu items to the admin menu
 *
 * @return array containing the menulinks for the admin menu items.
 */
function carts_adminapi_getmenulinks()
{
   /* $menulinks[] = array('url'   => xarModURL('carts','admin','configuration',array('gID' => 1)),
                         'title' => xarML('Administer the basket'),
                         'label' => xarML('basket'));*/
    $menulinks[] = array('url'   => xarModURL('carts','user','shopping_cart'),
                         'title' => xarML('View tour basket'),
                         'label' => xarML('View'));
	$menulinks[] = Array('url'   => xarModURL('vendors','admin','modifyconfig'),
						  'title' => xarML('Modify the configuration settings'),
						  'label' => xarML('Modify Config'));

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
