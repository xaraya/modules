<?php
/**
 * File: $Id: insertrecord.php,v 1.3 2003/07/18 19:40:41 garrett Exp $
 *
 * AddressBook user insertRecord
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
 * Inserts a record into the database
 *
 * @param mixed
 */
function addressbook_userapi_insertrecord($args) {
    extract($args);

    $lname = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$lname);
    $fname = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$fname);
    $title = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$title);
    $company = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$company);
    $zip = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$zip);
    $city = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$city);
    $address_1 = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$address_1);
    $address_2 = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$address_2);
    $state = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$state);
    $country = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$country);
    $contact_1 = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$contact_1);
    $contact_2 = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$contact_2);
    $contact_3 = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$contact_3);
    $contact_4 = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$contact_4);
    $contact_5 = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$contact_5);
    $note = xarModAPIFunc(__ADDRESSBOOK__,'user','securitycheck',$note);
    if (!isset($private)) { $private=0; }
    if (!xarUserIsLoggedIn()) { $user_id=0; }
    $last_updt = time();

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

    list($dbconn) = xarDBGetConn();
    $xarTables = xarDBGetTables();
    $address_table = $xarTables['addressbook_address'];

    $nextID = $dbconn->GenID($address_table);

    $sql = "INSERT INTO $address_table (
            nr,
            cat_id,
            prefix,
            lname,
            fname,
            sortname,
            title,
            company,
            sortcompany,
            img,
            zip,
            city,
            address_1,
            address_2,
            state,
            country,
            contact_1,
            contact_2,
            contact_3,
            contact_4,
            contact_5,
            c_label_1,
            c_label_2,
            c_label_3,
            c_label_4,
            c_label_5,
            c_main,";

// mising comma before 'array' was not flagged by compiler. is that normal PHP behaviour?
//            $sql .= xarModAPIFunc(__ADDRESSBOOK__,'user','getcustfieldinfo'array('flag'=>_AB_CUST_UDCOLNAMESONLY));
            if (isset($custUserData)) {
                $custColNames = xarModAPIFunc(__ADDRESSBOOK__,'user','getcustfieldinfo',
                                           array('flag'=>_AB_CUST_UDCOLNAMESONLY,'custUserData'=>$custUserData));
                foreach($custColNames as $colName) {
                    $sql .= $colName.",";
                }
            }

            $sql.="note,
            user_id,
            private,
            last_updt)
            VALUES (
            $nextID,
            ".xarVarPrepForStore($cat_id).",
            ".xarVarPrepForStore($prfx).",
            '".xarVarPrepForStore($lname)."',
            '".xarVarPrepForStore($fname)."',
            '".xarVarPrepForStore($sortvalue)."',
            '".xarVarPrepForStore($title)."',
            '".xarVarPrepForStore($company)."',
            '".xarVarPrepForStore($sortvalue2)."',
            '".xarVarPrepForStore($img)."',
            '".xarVarPrepForStore($zip)."',
            '".xarVarPrepForStore($city)."',
            '".xarVarPrepForStore($address_1)."',
            '".xarVarPrepForStore($address_2)."',
            '".xarVarPrepForStore($state)."',
            '".xarVarPrepForStore($country)."',
            '".xarVarPrepForStore($contact_1)."',
            '".xarVarPrepForStore($contact_2)."',
            '".xarVarPrepForStore($contact_3)."',
            '".xarVarPrepForStore($contact_4)."',
            '".xarVarPrepForStore($contact_5)."',
            ".xarVarPrepForStore($c_label_1).",
            ".xarVarPrepForStore($c_label_2).",
            ".xarVarPrepForStore($c_label_3).",
            ".xarVarPrepForStore($c_label_4).",
            ".xarVarPrepForStore($c_label_5).",
            ".xarVarPrepForStore($c_main).",";

            if (isset($custUserData)) {
                foreach($custUserData as $userData) {
                    if (strstr($userData['type'],_AB_CUST_TEST_STRING)) {
                        $sql .= "'".xarVarPrepForStore($userData['userData'])."',";

                    } elseif ($userData['type']=='date default NULL') {
                        $sql .= "'".xarModAPIFunc(__ADDRESSBOOK__,'util','td2stamp',array('idate'=>$userData['userData']))."',";

                    } elseif ($userData['type']=='int default NULL') {
                        $sql .= xarModAPIFunc(__ADDRESSBOOK__,'util','input2numeric',array('inum'=>$userData['userData'])).",";

                    } elseif ($userData['type']=='int(1) default NULL') {
                        $sql .= xarModAPIFunc(__ADDRESSBOOK__,'util','input2numeric',array('inum'=>$userData['userData'])).",";

                    } elseif ($userData['type']=='decimal(10,2) default NULL') {
                        $sql .= xarModAPIFunc(__ADDRESSBOOK__,'util','input2numeric',array('inum'=>$userData['userData'])).",";

                    } elseif ((!strstr($userData['type'],_AB_CUST_TEST_LB) &&
                               !strstr($userData['type'],_AB_CUST_TEST_HR)) &&
                              (empty($userData['userData']) || $userData['userData'] == '')) {
                        $sql .= $userData['colName'].'=NULL,';
                    }
// check was not stringent enough??
//                } elseif ((empty($userData['userData'])) || ($userData['userData'] == '')) {
//                    $sql .= 'NULL,';
//                }
                } // END foreach
            } // END if

    $sql.="'".xarVarPrepForStore($note)."',
            ".xarVarPrepForStore($user_id).",
            ".xarVarPrepForStore($private).",
            ".xarVarPrepForStore($last_updt).")";


    $result =& $dbconn->Execute($sql);
    if($dbconn->ErrorNo() != 0) { return false; }

    $result->Close();

    return true;
} // END insertrecord

?>
