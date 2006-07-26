<?php
/**
 * AddressBook user updateRecord
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
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
 * @todo MichelV: implement xarVarFetch here?
 */
function addressbook_userapi_updaterecord($args)
{
    extract($args);
    // TODO: MichelV: Can't these be replaced?
    $lname      = xarModAPIFunc('addressbook','user','securitycheck',$lname);
    $fname      = xarModAPIFunc('addressbook','user','securitycheck',$fname);
    $title      = xarModAPIFunc('addressbook','user','securitycheck',$title);
    $company    = xarModAPIFunc('addressbook','user','securitycheck',$company);
    $zip        = xarModAPIFunc('addressbook','user','securitycheck',$zip);
    $city       = xarModAPIFunc('addressbook','user','securitycheck',$city);
    $address_1  = xarModAPIFunc('addressbook','user','securitycheck',$address_1);
    $address_2  = xarModAPIFunc('addressbook','user','securitycheck',$address_2);
    $state      = xarModAPIFunc('addressbook','user','securitycheck',$state);
    $country    = xarModAPIFunc('addressbook','user','securitycheck',$country);
    $contact_1  = xarModAPIFunc('addressbook','user','securitycheck',$contact_1);
    $contact_2  = xarModAPIFunc('addressbook','user','securitycheck',$contact_2);
    $contact_3  = xarModAPIFunc('addressbook','user','securitycheck',$contact_3);
    $contact_4  = xarModAPIFunc('addressbook','user','securitycheck',$contact_4);
    $contact_5  = xarModAPIFunc('addressbook','user','securitycheck',$contact_5);
    $note       = xarModAPIFunc('addressbook','user','securitycheck',$note);
    if (!isset($private)) { $private=0; }
    $last_updt = time();
    // custom field values

    /**
     * custom field values
     */
    if (isset($custUserData)) {
        foreach($custUserData as $rowIdx=>$userData) {
            if (strstr($userData['custType'],_AB_CUST_TEST_STRING)) {
                $custUserData[$rowIdx]['userData'] =
                    xarModAPIFunc('addressbook','user','securitycheck',$userData['userData']);
            }
        }
    }

    // sort column
    if (xarModGetVar('addressbook', 'name_order')==1) {
        if (!empty($fname)) {
            $sortvalue = $fname.' '.$lname;
        } else {
            $sortvalue = $lname;
        }
    }
    else {
        if (!empty($lname)) {
            $sortvalue = $lname.', '.$fname;
        } else {
            $sortvalue = $fname;
        }
    }
    $special1 = xarModGetVar('addressbook', 'special_chars_1');
    $special2 = xarModGetVar('addressbook', 'special_chars_2');
    for ($i=0;$i<strlen($special1);$i++) {
        $a[substr($special1,$i,1)]=substr($special2,$i,1);
    }
    if (isset($a) && is_array($a)) {
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
            if (strstr($userData['custType'],_AB_CUST_TEST_STRING)) {
                array_push ($bindvars, $userData['userData']);

            } elseif ($userData['custType']==_AB_CUSTOM_DATE) {
                array_push ($bindvars, xarModAPIFunc('addressbook','util','td2stamp',array('idate'=>$userData['userData'])));

            } elseif ($userData['custType']==_AB_CUSTOM_INTEGER) {
                array_push ($bindvars, xarModAPIFunc('addressbook','util','input2numeric',array('inum'=>$userData['userData'])));

            } elseif ($userData['custType']==_AB_CUSTOM_CHECKBOX) {
                if (isset($userData['userData'])) {
                    array_push ($bindvars, xarModAPIFunc('addressbook','util','input2numeric',array('inum'=>$userData['userData'])));
                } else {
                    array_push ($bindvars, 'NULL');
                }

            } elseif ($userData['custType']==_AB_CUSTOM_DECIMAL) {
                array_push ($bindvars, xarModAPIFunc('addressbook','util','input2numeric',array('inum'=>$userData['userData'])));

            } elseif ((!strstr($userData['custType'],_AB_CUSTOM_BLANKLINE) &&
                       !strstr($userData['custType'],_AB_CUSTOM_HORIZ_RULE)) &&
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
