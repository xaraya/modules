<?php
/**
 * File: $Id: xaradminapi.php,v 1.3 2003/06/30 04:37:08 garrett Exp $
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
 *
 */
function AddressBook_userapi_getDetailValues($args) {
    extract($args);

    list($dbconn) = xarDBGetConn();
    $xarTables = xarDBGetTables();
    $address_table = $xarTables['addressbook_address'];

    $sql = "SELECT * FROM $address_table WHERE (nr=".$id.")";
    $result =& $dbconn->Execute($sql);

    if($dbconn->ErrorNo() != 0) { //gehDEBUG
        xarExceptionSet(XAR_USER_EXCEPTION, _AB_ERR_ERROR, new abUserException("sql = ".$sql));
        return array();
    }

    if(!isset($result)) { return; }

    list($id,$cat_id,$prefix,$lname,$fname,$sortname,$title,$company,$sortcompany,$img,$zip,$city,$address_1,$address_2,$state,$country,$contact_1,$contact_2,$contact_3,$contact_4,$contact_5,$c_label_1,$c_label_2,$c_label_3,$c_label_4,$c_label_5,$c_main,$custom_1,$custom_2,$custom_3,$custom_4,$note,$user,$private,$date) = $result->fields;

    /**
     * get the custom field information
     */
    $custUserData = xarModAPIFunc(__ADDRESSBOOK__,'user','getCustFieldInfo',array('id'=>$id));

    $detailValues = array('cat_id'=>$cat_id,
                        'prfx'=>$prefix,
                        'lname'=>$lname,
                        'fname'=>$fname,
                        'sortname'=>$sortname,
                        'title'=>$title,
                        'company'=>$company,
                        'sortcompany'=>$sortcompany,
                        'img'=>$img,
                        'zip'=>$zip,
                        'city'=>$city,
                        'address_1'=>$address_1,
                        'address_2'=>$address_2,
                        'state'=>$state,
                        'country'=>$country,
                        'contact_1'=>$contact_1,
                        'contact_2'=>$contact_2,
                        'contact_3'=>$contact_3,
                        'contact_4'=>$contact_4,
                        'contact_5'=>$contact_5,
                        'c_label_1'=>$c_label_1,
                        'c_label_2'=>$c_label_2,
                        'c_label_3'=>$c_label_3,
                        'c_label_4'=>$c_label_4,
                        'c_label_5'=>$c_label_5,
                        'c_main'=>$c_main,
                        'custUserData'=>$custUserData,
                        'note'=>$note,
                        'user'=>$user,
                        'private'=>$private,
                        'date'=>$date);

    $result->Close();
    return $detailValues;
} // END getDetailValues

?>