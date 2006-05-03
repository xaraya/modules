<?php
/**
 * Xaraya Referers
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2006 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 * @subpackage Referer Module
 * @author John Cox et al. 
 */
/**
 * utility function pass individual menu items to the admin panels
 * 
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function referer_adminapi_getmenulinks()
{
    if (xarSecurityCheck('EditReferer', 0)) {
        $menulinks[] = Array('url' => xarModURL('referer',
                'admin',
                'view'),
            'title' => xarML('View Referers'),
            'label' => xarML('View'));
    } 

    if (xarSecurityCheck('AdminReferer', 0)) {
        $menulinks[] = Array('url' => xarModURL('referer',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration'),
            'label' => xarML('Modify Config'));
    } 

    if (empty($menulinks)) {
        $menulinks = '';
    } 

    return $menulinks;
} 

?>