<?php
/**
 * File: $Id$
 *
 * Sniffer Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Sniffer Module
 * @author Frank Besler
 *
 * Using phpSniffer by Roger Raymond
 * Purpose of file: find out the browser and OS of the visitor
*/


/**
 * Utility function pass individual menu items to the main menu
 *
 * @public
 * @author Richard Cave 
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function sniffer_adminapi_getmenulinks()
{

    if (xarSecurityCheck('AdminSniffer')) {
        $menulinks[] = Array('url'   => xarModURL('sniffer',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View browser and OS of visitors.'),
                              'label' => xarML('View Sniffs'));

        // Check that the GD library is available
        if (extension_loaded('gd')) {
            $menulinks[] = Array('url'   => xarModURL('sniffer',
                                                      'admin',
                                                      'chart'),
                                 'title' => xarML('Chart browser and OS of visitors.'),
                                 'label' => xarML('Chart Sniffs'));
        }
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
