<?PHP
// $Id: pnadmin.php
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Filename: 1.0
// Based on : pnDProject
// Purpose of file:  Admin functions for xardplink
// ----------------------------------------------------------------------
/**
 * Standard Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarDPLink Module
 * @link http://xaraya.com/index.php/release/591.html
 * @author xarDPLink Module Development Team
 */
/**
 * the main administration function
 */
function xardplink_admin_main()
{
    if (!xarSecurityCheck('AdminXardplink')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        /* If you want to go directly to some default function, instead of
         * having a separate main function, you can simply call it here, and
         * use the same template for admin-main.xd as for admin-view.xd
         * return xarModFunc('example','admin','view');
         */

        /* Initialise the $data variable that will hold the data to be used in
         * the blocklayout template, and get the common menu configuration - it
         * helps if all of the module pages have a standard menu at the top to
         * support easy navigation
         */
        $data = array();
        return $data;
    } else {
        /* If the Overview documentation is turned off, then we just return the view page,
         * or whatever function seems to be the most fitting.
         */
        xarResponseRedirect(xarModURL('xardplink', 'admin', 'modifyconfig'));
    }
    /* success so return true */
    return true;


}
?>
