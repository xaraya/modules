<?php
/**
 * Photoshare by Chris van de Steeg
 * based on Jorn Lind-Nielsen 's photoshare
 * module for PostNuke
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage photoshare
 * @author Chris van de Steeg
 */

function photoshare_userapi_getmenulinks()
{
    $menulinks = array();

    // Security Check
    if (!xarSecurityCheck('ViewFolder',0)) {
        return $menulinks;
    }

    $menulinks[] = Array('url'   => xarModURL('photoshare',
                                              'user',
                                              'viewallfolders'),
                         'title' => xarML('View all photo albums'),
                         'label' => xarML('All albums'));

    if (xarSecurityCheck('EditFolder',0)) {
        $menulinks[] = Array('url'   => xarModURL('photoshare',
                                                  'user',
                                                  'view'),
                             'title' => xarML('Edit your own album'),
                             'label' => xarML('My Albums'));
    }
    return $menulinks;
}

?>
