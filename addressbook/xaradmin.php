<?php
/**
 * File: $Id: xaradmin.php,v 1.3 2003/07/09 11:20:20 garrett Exp $
 *
 * AddressBook admin functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

//FIXME: until we figure out module globals
include_once ('modules/addressbook/xarglobal.php');

//=========================================================================
//  the main administration function
//=========================================================================
function addressbook_admin_main() {

    /**
     * Check if we want to display our overview panel.
     */
    if (xarModGetVar('adminpanels', 'overview') == 0){
        return array();
    } else {
        xarResponseRedirect(xarModURL(__ADDRESSBOOK__,'admin','modifyconfig'));
    }

} // END main

/**
 * Placeholder function to display a static page
 */
function addressbook_admin_displayDocs () {

    return array();

} // END displayDocs

?>