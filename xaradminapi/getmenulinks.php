<?php
/*
 *
 * Polls Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function polls_adminapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('AddPolls',0)) {
        $menulinks[] = Array('url'   => xarModURL('polls',
                                                   'admin',
                                                   'new'),
                              'title' => xarML('Create a New Poll'),
                              'label' => xarML('New Poll'));
    }
    if (xarSecurityCheck('EditPolls',0)) {
        $menulinks[] = Array('url'   => xarModURL('polls',
                                                   'admin',
                                                   'list'),
                              'title' => xarML('View a list of previous polls'),
                              'label' => xarML('List Polls'));
    }
    if (xarSecurityCheck('AdminPolls',0)) {
        $menulinks[] = Array('url' => xarModURL('polls',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify Polls configuration'),
                              'label' => xarML('Modify Config'));
    }
    return $menulinks;
}

?>
