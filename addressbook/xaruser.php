<?php
/**
 * File: $Id: xaruser.php,v 1.2 2003/07/02 08:07:14 garrett Exp $
 *
 * AddressBook utility functions
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
//  the main function
//=========================================================================
function AddressBook_user_main() {

    return xarModFunc(__ADDRESSBOOK__,'user','viewall');

} // END main

?>