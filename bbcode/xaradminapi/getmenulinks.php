<?php
/**
 * File: $Id$
 * 
 * Xaraya BBCode
 * Based on pnBBCode Hook from larseneo
 * Converted to Xaraya by John Cox
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage BBCode Module
 * @author larseneo
*/
/**
 * utility function pass individual menu items to the admin panels
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function bbcode_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('EditBBCode', 0)) {
        $menulinks[] = Array('url' => xarModURL('bbcode',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration for the bbcode module'),
            'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
}
?>