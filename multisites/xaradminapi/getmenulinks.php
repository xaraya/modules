<?php
/* File: $Id$
 * Xaraya Multisites
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Multisites Module
 * @author
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function multisites_adminapi_getmenulinks()
{

    if (xarSecurityCheck('AddMultisites',0)) {

        $menulinks[] = Array('url'   => xarModURL('multisites',
                                                   'admin',
                                                   'addsite'),
                              'title' => xarML('Add a new site into the system'),
                              'label' => xarML('Add Site'));
    }

    if (xarSecurityCheck('ReadMultisites',0)) {

        $menulinks[] = Array('url'   => xarModURL('multisites',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('View and Edit Sites'),
                              'label' => xarML('View Sites'));
    }

    if (xarSecurityCheck('AdminMultisites',0)) {
        $menulinks[] = Array('url'   => xarModURL('multisites',
                                                   'admin',
                                                   'adminconfig'),
                              'title' => xarML('Modify the admin settings for Multisites'),
                              'label' => xarML('Admin Config'));
    }
    if (xarSecurityCheck('AdminMultisites',0)) {
        $menulinks[] = Array('url'   => xarModURL('multisites',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Configure Master Site'),
                              'label' => xarML('Master Config'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
