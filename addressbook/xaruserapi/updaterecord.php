<?php
/**
 * File: $Id: updaterecord.php,v 1.2 2004/03/28 23:23:16 garrett Exp $
 *
 * AddressBook user updateRecord
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
 * Updates a record in the database
 *
 * @param mixed
 *
 */
function addressbook_userapi_updaterecord($args)
{
    extract($args);

    $lname      = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$lname);
    $fname      = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$fname);
    $title      = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$title);
    $company    = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$company);
    $zip        = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$zip);
    $city       = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$city);
    $address_1  = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$address_1);
    $address_2  = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$address_2);
    $state      = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$state);
    $country    = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$country);
    $contact_1  = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$contact_1);
    $contact_2  = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$contact_2);
    $contact_3  = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$contact_3);
    $contact_4  = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$contact_4);
    $contact_5  = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$contact_5);
    $note       = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$note);
    if (!isset($private)) { $private=0; }
    $last_updt = time();
    // custom field values

    /**
     * custom field values
     */
    if (isset($custUserData)) {
        foreach($custUserData as $rowIdx=>$userData) {
            if (strstr($userData['type'],_AB_CUST_TEST_STRING)) {
                $custUserData[$rowIdx]['userData'] =
                    xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$userData['userData']);
            }
        }
    }

    // sort column
    if (xarModGetVar(__ADDRESSBOOK__, 'name_order')==1) {
        $sortvalue = $fname.' '.$lname;
    }
    else {
        $sortvalue = $lname.', '.$fname;
    }
    $special1 = xarModGetVar(__ADDRESSBOOK__, 'special_chars_1');
    $special2 = xarModGetVar(__ADDRESSBOOK__, 'special_chars_2');
    for ($i=0;$i<strlen($special1);$i++) {
        $a[substr($special1,$i,1)]=substr($special2,$i,1);
    }
    if (is_array($a)) {
        $sortvalue = strtr($sortvalue, $a);
        $sortvalue2 = strtr($company, $a);
    }
    else {
        $sortvalue2 = $company;
    }

    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $address_table = $xarTables['addressbook_address'];

    $sql = "UPDATE $address_table
               SET  cat_id      = ?
                   ,prefix      = ?
                   ,lname       = ?
                   ,fname       = ?
                   ,sortname    = ?
                   ,title       = ?
                   ,company     = ?
                   ,sortcompany = ?
                   ,img         = ?
                   ,zip         = ?
                   ,city        = ?
                   ,address_1   = ?
                   ,address_2   = ?
                   ,state       = ?
                   ,country     = ?
                   ,contact_1   = ?
                   ,contact_2   = ?
                   ,contact_3   = ?
                   ,contact_4   = ?
                   ,contact_5   = ?
                   ,c_label_1   = ?
                   ,c_label_2   = ?
                   ,c_label_3   = ?
                   ,c_label_4   = ?
                   ,c_label_5   = ?
                   ,c_main      = ?";

    if (isset($custUserData)) {
        foreach($custUserData as $userData) {
            $sql .= ",".$userData['colName']." = ?";
        }
    }

          $sql .= ",note        = ?
                   ,private     = ?
                   ,last_updt   = ?
             WHERE nr = ?";

    $bindvars = array ($cat_id
                      ,$prfx
                      ,$lname
                      ,$fname
                      ,$sortvalue
                      ,$title
                      ,$company
                      ,$sortvalue2
                      ,$img
                      ,$zip
                      ,$city
                      ,$address_1
                      ,$address_2
                      ,$state
                      ,$country
                      ,$contact_1
                      ,$contact_2
                      ,$contact_3
                      ,$contact_4
                      ,$contact_5
                      ,$c_label_1
                      ,$c_label_2
                      ,$c_label_3
                      ,$c_label_4
                      ,$c_label_5
                      ,$c_main);

    if (isset($custUserData)) {
        foreach($custUserData as $userData) {
            if (strstr($userData['type'],_AB_CUST_TEST_STRING)) {
                array_push ($bindvars, $userData['userData']);

            } elseif ($userData['type']=='date default NULL') {
                array_push ($bindvars, xarModAPIFunc(__ADDRESSBOOK__,'util','td2stamp',array('idate'=>$userData['userData'])));

            } elseif ($userData['type']=='int default NULL') {
                array_push ($bindvars, xarModAPIFunc(__ADDRESSBOOK__,'util','input2numeric',array('inum'=>$userData['userData'])));

            } elseif ($userData['type']=='int(1) default NULL') {
                array_push ($bindvars, xarModAPIFunc(__ADDRESSBOOK__,'util','input2numeric',array('inum'=>$userData['userData'])));

            } elseif ($userData['type']=='decimal(10,2) default NULL') {
                array_push ($bindvars, xarModAPIFunc(__ADDRESSBOOK__,'util','input2numeric',array('inum'=>$userData['userData'])));

            } elseif ((!strstr($userData['type'],_AB_CUST_TEST_LB) &&
                       !strstr($userData['type'],_AB_CUST_TEST_HR)) &&
                      (empty($userData['userData']) || $userData['userData'] == '')) {
                array_push ($bindvars, 'NULL');
            }
        } // END foreach
    } // END if

    array_push ($bindvars, $note);
    array_push ($bindvars, $private);
    array_push ($bindvars, $last_updt);

    //WHERE clause
    array_push ($bindvars, $id);

    //end bindvars

    $result =& $dbconn->Execute($sql, $bindvars);
    if($dbconn->ErrorNo() != 0) { return false; }

    $result->Close();
    return true;
} // END updaterecord

?>
