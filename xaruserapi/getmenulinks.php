<?php
/**
 *
 * Function purpose to be added
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Gmaps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  to be added
 *
 * @param to be added
 * @return to be added
 *
 */

function gmaps_userapi_getmenulinks()
{

    if (xarSecurityCheck('ViewGmaps',0)) {
        $menulinks[] = Array('url'   => xarModURL('gmaps',
                                                  'user',
                                                  'main'),
                              'title' => xarML(''),
                              'label' => xarML(''));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}

?>
