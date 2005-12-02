<?php
/**
 * File: $Id:
 * 
 * Utility function to pass menu items to the main menu
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */
/**
 * utility function pass individual menu items to the main menu
 * 
 * @author curtisdf 
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function bible_adminapi_getmenulinks()
{
	$menulinks = array();

    // Security Check
    if (xarSecurityCheck('AdminBible', 0)) {
        $menulinks[] = Array('url' => xarModURL('bible', 'admin', 'overview'),
            'title' => xarML('Overview'),
            'label' => xarML('Overview'));

        $menulinks[] = Array('url' => xarModURL('bible', 'admin', 'view'),
            'title' => xarML('View all Bible texts that have been added.'),
            'label' => xarML('View Texts'));

        $menulinks[] = Array('url' => xarModURL('bible', 'admin', 'aliases'),
            'title' => xarML('Configure book aliases'),
            'label' => xarML('Book Aliases'));

        $menulinks[] = Array('url' => xarModURL('bible', 'admin', 'modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }

    return $menulinks;
} 

?>
