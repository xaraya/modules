<?php
/**
 *
 * Function purpose to be added
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Maps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  to be added
 *
 * @param to be added
 * @return to be added
 *
 */

function maps_userapi_getmenulinks()
{

    if (xarSecurityCheck('ViewMaps',0)) {
        $menulinks[] = Array('url'   => xarModURL('maps',
                                                  'user',
                                                  'main'),
                              'title' => xarML('Short description of this module'),
                              'label' => xarML('Overview'));
    }
    if (xarSecurityCheck('ReadMaps',0)) {
        $menulinks[] = Array('url'   => xarModURL('maps',
                                                  'user',
                                                  'display'),
                              'title' => xarML('Show a map'),
                              'label' => xarML('Display'));
    }
    if (xarSecurityCheck('DeleteMaps',0)) {
        $menulinks[] = Array('url'   => xarModURL('maps',
                                                  'user',
                                                  'manage'),
                              'title' => xarML('Manage a map'),
                              'label' => xarML('Manage'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}

?>
