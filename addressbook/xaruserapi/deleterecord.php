<?php
/**
 * File: $Id: modifyconfig.php,v 1.3 2003/07/02 02:15:15 garrett Exp $
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

/**
 * deleterecord
 */
function AddressBook_userapi_deleterecord($args) {
    extract($args);

    list($dbconn) = xarDBGetConn();
    $xarTables = xarDBGetTables();
    $address_table = $xarTables['addressbook_address'];

    $sql = "DELETE FROM $address_table WHERE nr=$id";

    $result =& $dbconn->Execute($sql);

    if($dbconn->ErrorNo() != 0) { return false; }

    $result->Close();
    return true;
} // END deleterecord

?>