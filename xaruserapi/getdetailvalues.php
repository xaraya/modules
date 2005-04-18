<?php
/**
 * File: $Id: getdetailvalues.php,v 1.2 2004/03/28 23:23:16 garrett Exp $
 *
 * AddressBook user getDetailValues
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
 * Retrieve detail values for a given record
 *
 * @param int $id of the target record
 * @return array $detailValues
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function addressbook_userapi_getDetailValues($args) 
{

    $detailValues = FALSE;

    /**
     * Security check
     */
    if (!xarSecurityCheck('ReadAddressBook',0)) return FALSE;

    extract($args);

    $invalid = array();
    if (!isset($id)) { $invalid[] = 'id'; }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
                     join(', ',$invalid), 'getDetailValues', __ADDRESSBOOK__);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($msg));
        return FALSE;
    } else {

        $dbconn =& xarDBGetConn();
        $xarTables =& xarDBGetTables();
        $address_table = $xarTables['addressbook_address'];

        $sql = "SELECT * FROM $address_table WHERE (nr=".$id.")";
        $result =& $dbconn->Execute($sql);
        if(!$result) { return FALSE; }

        $detailValues = array();
        list($detailValues['id']
            ,$detailValues['cat_id']
            ,$detailValues['prfx']
            ,$detailValues['lname']
            ,$detailValues['fname']
            ,$detailValues['sortname']
            ,$detailValues['title']
            ,$detailValues['company']
            ,$detailValues['sortcompany']
            ,$detailValues['img']
            ,$detailValues['zip']
            ,$detailValues['city']
            ,$detailValues['address_1']
            ,$detailValues['address_2']
            ,$detailValues['state']
            ,$detailValues['country']
            ,$detailValues['contact_1']
            ,$detailValues['contact_2']
            ,$detailValues['contact_3']
            ,$detailValues['contact_4']
            ,$detailValues['contact_5']
            ,$detailValues['c_label_1']
            ,$detailValues['c_label_2']
            ,$detailValues['c_label_3']
            ,$detailValues['c_label_4']
            ,$detailValues['c_label_5']
            ,$detailValues['c_main']
            ,$detailValues['custom_1']
            ,$detailValues['custom_2']
            ,$detailValues['custom_3']
            ,$detailValues['custom_4']
            ,$detailValues['note']
            ,$detailValues['user']
            ,$detailValues['private']
            ,$detailValues['last_updt']
            ) = $result->fields;

        /**
         * get the custom field information
         */
        $detailValues['custUserData'] = xarModAPIFunc(__ADDRESSBOOK__,'user','getcustfieldinfo',array('id'=>$id));

        $result->Close();
    }

    return $detailValues;

} // END getDetailValues

?>