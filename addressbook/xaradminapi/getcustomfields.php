<?php
/**
 * File: $Id: getcustomfields.php,v 1.2 2003/12/22 07:12:49 garrett Exp $
 *
 * AddressBook admin getCustomFields
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

function addressbook_adminapi_getCustomfields() {
    $dbconn =& xarDBGetConn();
    $xarTables = xarDBGetTables();
    $cus_table = $xarTables['addressbook_customfields'];
    $sql = "SELECT nr, label, type, position
            FROM $cus_table WHERE nr > 0
            ORDER BY position";

    $result =& $dbconn->Execute($sql);
    if (!$result) return array();

    $customfields = array();
    for($i=0; !$result->EOF; $result->MoveNext()) {
        list($cusid,$cuslabel,$custype,$cuspos) = $result->fields;
        $customfields[$i]['nr']     = $cusid;
        $customfields[$i]['custLabel']   = $cuslabel;
        $customfields[$i]['custType']   = $custype;
        $customfields[$i++]['position']   = $cuspos;
     }
    $result->Close();
    return $customfields;
} // END getCustomFields

?>