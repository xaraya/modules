<?php
/**
 * File: $Id: getcustfieldtypeinfo.php,v 1.1 2003/07/07 04:11:58 garrett Exp $
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
 * getCustomFieldsTypes
 */
function addressbook_userapi_getCustFieldTypeInfo() {

    $custFieldTypeInfo = array();

    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $tableCustomField = $xarTables['addressbook_customfields'];

    $sql = "SELECT nr, label, type, position ".
           "FROM $tableCustomField WHERE nr > 0 ".
           "ORDER BY position";
    $result =& $dbconn->Execute($sql);

    if($dbconn->ErrorNo() == 0) {
        if(isset($result)) {
            for($i=0; !$result->EOF; $result->MoveNext()) {
                list($cusid,$cuslabel,$custype,$cuspos) = $result->fields;
                $custFieldTypeInfo[$i]['nr']     = $cusid;
                $custFieldTypeInfo[$i]['colName']= _AB_CUST_COLPREFIX.$cusid;
                $custFieldTypeInfo[$i]['label']  = $cuslabel;
                $custFieldTypeInfo[$i]['type']   = $custype;
                $custFieldTypeInfo[$i++]['position']   = $cuspos;
             }
            $result->Close();
        }
    }

    return $custFieldTypeInfo;

} // END getCustFieldTypeInfo

?>