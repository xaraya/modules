<?php
/**
 * Figlet
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage Figlet
 * @author Lucas Baltes, John Cox
 *
 */
/**
 * utility function pass individual menu items to the admin panels
 * @return array containing the menulinks for the main menu items.
 */
function figlet_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('AdminFiglet',0)) {
        $menulinks[] = Array('url'   => xarModURL('figlet',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the configuration for the figlet module'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>
