<?php
/**
 * AddressBook utility functions
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * getCustomFieldsTypes
 */
function addressbook_userapi_getCustFieldTypeInfo()
{
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $cus_table = $xarTables['addressbook_customfields'];
    $sql = "SELECT *
            FROM $cus_table WHERE nr > 0
            ORDER BY position";

    $result =& $dbconn->Execute($sql);
    if (!$result) return array();

    $customFields = array();
    for($i=0; !$result->EOF; $result->MoveNext(), $i++) {
        list($custID,$custLabel,$custType,$position, $custShortLabel, $custDisplay) = $result->fields;

        $custFieldData[$i]['nr']            = $custID;
        $custFieldData[$i]['colName']       = _AB_CUST_COLPREFIX.$custID;
        $custFieldData[$i]['custLabel']     = $custLabel;
        $custFieldData[$i]['custType']      = $custType;
        $custFieldData[$i]['position']      = $position;
        $custFieldData[$i]['custShortLabel']= $custShortLabel;
        $custFieldData[$i]['custDisplay']   = $custDisplay;
    }
    $result->Close();

    return $custFieldData;

} // END getCustFieldTypeInfo

?>
