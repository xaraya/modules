<?php
/**
 * Utility function to pass individual menu items to main menu
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage Wizards Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function wizards_adminapi_getmenulinks()
{

// Security Check
    if (xarSecurityCheck('AdminWizard',0)) {
        $menulinks[] = Array('url'   => xarModURL('wizards',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the user module configuration'),
                              'label' => xarML('Modify Config'));
    }

// Security Check
    $wizards = xarModGetVar('wizards','status');
    if (xarSecurityCheck('EditWizard',0) && ($wizards - ($wizards % 2))/2
) {
        $menulinks[] = Array('url'   => xarModURL('wizards',
                                                  'admin',
                                                  'listscripts',
                                                  array('info' => xarRequestGetInfo())),
                              'title' => xarML('Display the wizards available to this module'),
                              'label' => xarML('Wizards'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>