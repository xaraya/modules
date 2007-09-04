<?php
/**
 * AddressBook user functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
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
    if (!xarVarFetch('size', 'int', $size, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('value', 'int', $value, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('company', 'str', $company, $company, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('multiple', 'str', $multiple, $multiple, XARVAR_NOT_REQUIRED)) return;
    
    $data = array();

    $items = array();
    
    if(isset($company)) {
    
        if (!isset($options) || count($options) == 0) {
            $addresslist = xarModAPIFunc('addressbook','user','getall',array('company'=>$company));
    
            if (!isset($addresslist)) return;
            
            $data['options'] = $addresslist;
            
        } else {
            $data['options'] = $options;
        }
    } else {
        $data['options'] = array();
    }

    $data['company'] = $company;
    $data['value'] = $value;
    $data['fieldname'] = $fieldname;
    $data['fieldid'] = $fieldid;
    $data['multiple'] = $multiple ? $multiple : "";
    $data['size'] = $size;

    return $data;
} // END main

?>
