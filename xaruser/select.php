<?php
/**
 * AddressBook user functions
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

function AddressBook_user_select($args)
{
    extract($args);

    if (!xarVarFetch('fieldname', 'str:1:', $fieldname)) return;
    if (!xarVarFetch('fieldid', 'int', $fieldid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('value', 'int', $value, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('company', 'str', $company, $company, XARVAR_NOT_REQUIRED)) return;

    $data = array();

    $items = array();

    if(!empty($company)) {
        $addresslist = xarModAPIFunc('addressbook','user','getall',array('company'=>$company));
        array_shift($addresslist);
        $instructions = array('id'=>'0','displayName'=>xarML('Select a contact...'));
        array_unshift($addresslist, $instructions);

        if (!isset($addresslist)) return;
    } else {
        $addresslist = array();
    }

    $data['value'] = $value ? $value : "";
    $data['options'] = $addresslist;
    $data['company'] = $company;
    $data['fieldname'] = $fieldname;
    $data['fieldid'] = $fieldid;

    return $data;
} // END main

?>
