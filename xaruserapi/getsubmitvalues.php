<?php
/**
 * AddressBook utility functions
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
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
function addressbook_userapi_getsubmitvalues($args)
{

    extract($args);

    if (!xarVarFetch ('id','int::',         $output['id'], FALSE)) return;
    if (!xarVarFetch ('cat_id','int::',     $output['cat_id'], FALSE)) return;

    if (!xarVarFetch ('prfx','int::',       $output['prfx'], 0)) return;
    if (!xarVarFetch ('lname','str::',      $output['lname'], FALSE)) return;
    if (!xarVarFetch ('fname','str::',      $output['fname'], FALSE)) return;
    if (!xarVarFetch ('title','str::',      $output['title'], FALSE)) return;
    if (!xarVarFetch ('company','str::',    $output['company'], FALSE)) return;
    if (!xarVarFetch ('img','str::',        $output['img'], FALSE)) return;

    if (!xarVarFetch ('address_1','str::',  $output['address_1'], FALSE)) return;
    if (!xarVarFetch ('address_2','str::',  $output['address_2'], FALSE)) return;
    if (!xarVarFetch ('city','str::',       $output['city'], FALSE)) return;
    if (!xarVarFetch ('state','str::',      $output['state'], FALSE)) return;
    if (!xarVarFetch ('zip','str::',        $output['zip'], FALSE)) return;
    if (!xarVarFetch ('country','str::',    $output['country'], FALSE)) return;

    if (!xarVarFetch ('contact_1','str::',  $output['contact_1'], FALSE)) return;
    if (!xarVarFetch ('contact_2','str::',  $output['contact_2'], FALSE)) return;
    if (!xarVarFetch ('contact_3','str::',  $output['contact_3'], FALSE)) return;
    if (!xarVarFetch ('contact_4','str::',  $output['contact_4'], FALSE)) return;
    if (!xarVarFetch ('contact_5','str::',  $output['contact_5'], FALSE)) return;
    if (!xarVarFetch ('c_label_1','int::',  $output['c_label_1'], '1')) return;
    if (!xarVarFetch ('c_label_2','int::',  $output['c_label_2'], '2')) return;
    if (!xarVarFetch ('c_label_3','int::',  $output['c_label_3'], '3')) return;
    if (!xarVarFetch ('c_label_4','int::',  $output['c_label_4'], '4')) return;
    if (!xarVarFetch ('c_label_5','int::',  $output['c_label_5'], '5')) return;
    if (!xarVarFetch ('c_main','str::',     $output['c_main'], '0')) return;

    if (!xarVarFetch ('note','str::',       $output['note'], FALSE)) return;

    if (!xarVarFetch ('private','str::',      $output['private'], 0)) return;
    if (!xarVarFetch ('formcall','str::',     $output['formcall'], FALSE)) return;
    if (!xarVarFetch ('formSubmitted','bool::',     $output['formSubmitted'], FALSE)) return;
    if (!xarVarFetch ('action','int::',       $output['action'], _AB_TEMPLATE_NAME)) return;

    if (!xarVarFetch ('user_id','int::',      $output['user_id'], FALSE)) return;

    /**
     * Retrieve custom field values
     */
    $custom_tab = xarModGetVar('addressbook','custom_tab');
    if (!empty($custom_tab)) {
        if (!xarVarFetch ('custUserData','array::',$output['custUserData'], array())) return;
    } // END if

    return $output;

} // END getsubmitvalues

?>