<?php
/**
 * File: $Id: xaradminapi.php,v 1.3 2003/06/30 04:37:08 garrett Exp $
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
 * Retrieves all form input from the GET / POST and formats 
 *
 * @param mixed
 * @return mixed
 */
function AddressBook_userapi_getsubmitvalues($args) {

    extract($args);

    if (!xarVarFetch ('id','int::',         $data['id'], FALSE)) return;
    if (!xarVarFetch ('cat_id','int::',     $data['cat_id'], FALSE)) return;

    if (!xarVarFetch ('prfx','int::',       $data['prfx'], 0)) return;
    if (!xarVarFetch ('lname','str::',      $data['lname'], FALSE)) return;
    if (!xarVarFetch ('fname','str::',      $data['fname'], FALSE)) return;
    if (!xarVarFetch ('title','str::',      $data['title'], FALSE)) return;
    if (!xarVarFetch ('company','str::',    $data['company'], FALSE)) return;
    if (!xarVarFetch ('img','str::',        $data['img'], FALSE)) return;

    if (!xarVarFetch ('address_1','str::',  $data['address_1'], FALSE)) return;
    if (!xarVarFetch ('address_2','str::',  $data['address_2'], FALSE)) return;
    if (!xarVarFetch ('city','str::',       $data['city'], FALSE)) return;
    if (!xarVarFetch ('state','str::',      $data['state'], FALSE)) return;
    if (!xarVarFetch ('zip','str::',        $data['zip'], FALSE)) return;
    if (!xarVarFetch ('country','str::',    $data['country'], FALSE)) return;

    if (!xarVarFetch ('contact_1','str::',  $data['contact_1'], FALSE)) return;
    if (!xarVarFetch ('contact_2','str::',  $data['contact_2'], FALSE)) return;
    if (!xarVarFetch ('contact_3','str::',  $data['contact_3'], FALSE)) return;
    if (!xarVarFetch ('contact_4','str::',  $data['contact_4'], FALSE)) return;
    if (!xarVarFetch ('contact_5','str::',  $data['contact_5'], FALSE)) return;
    if (!xarVarFetch ('c_label_1','int::',  $data['c_label_1'], '1')) return;
    if (!xarVarFetch ('c_label_2','int::',  $data['c_label_2'], '2')) return;
    if (!xarVarFetch ('c_label_3','int::',  $data['c_label_3'], '3')) return;
    if (!xarVarFetch ('c_label_4','int::',  $data['c_label_4'], '4')) return;
    if (!xarVarFetch ('c_label_5','int::',  $data['c_label_5'], '5')) return;
    if (!xarVarFetch ('c_main','str::',     $data['c_main'], '0')) return;

    if (!xarVarFetch ('note','str::',       $data['note'], FALSE)) return;

    if (!xarVarFetch ('private','str::',     $data['private'], 0)) return;
    if (!xarVarFetch ('date','str::',     $data['date'], FALSE)) return;
    if (!xarVarFetch ('formcall','str::',     $data['formcall'], FALSE)) return;
    if (!xarVarFetch ('formSubmitted','bool::',     $data['formSubmitted'], FALSE)) return;
    if (!xarVarFetch ('action','int::',       $data['action'], _AB_TEMPLATE_NAME)) return;

    if (!xarVarFetch ('user_id','int::',     $data['user_id'], FALSE)) return;

    /**
     * Retrieve custom field values
     */
    $custom_tab = xarModGetVar(__ADDRESSBOOK__,'custom_tab');
    if (!empty($custom_tab)) {
        if (!xarVarFetch ("custUserData",'array::',$data['custUserData'], FALSE)) {
            xarExceptionSet(XAR_USER_EXCEPTION, _AB_ERR_ERROR,
                            new abUserException(array('file' => __FILE__
                                                      ,'line' => __LINE__
                                                      ,'data' => $custUserData))); //gehDEBUG
        }
    } // END if

    return $data;

} // END getsubmitvalues

?>