<?php
/**
 * Standard function to get main menu links
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage Wizards Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function wizards_userapi_getmenulinks()
{

// Security Check
    $wizards = xarModGetVar('wizards','status');
    if (xarSecurityCheck('ViewWizards',0) &&  ($wizards % 2)) {
        $menulinks[] = Array('url'   => xarModURL('wizards',
                                                  'user',
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