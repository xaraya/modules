<?php
/**
 * File: $Id: confirmdelete.php,v 1.3 2003/12/22 07:12:50 garrett Exp $
 *
 * AddressBook user confirmDelete
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

//=========================================================================
//  Confirm deletion
//=========================================================================
function addressbook_user_confirmdelete() {

    $output = array();

    // preserve menu settings
    $menuValues = xarModAPIFunc(__ADDRESSBOOK__,'user','getmenuvalues');
    foreach ($menuValues as $key=>$value) {
        $output[$key] = $value;
    }

    $output['menuValues']=array('catview'   =>$output['catview'],
                    'menuprivate'=>$output['menuprivate'],
                    'all'       =>$output['all'],
                    'sortview'  =>$output['sortview'],
                    'page'      =>$output['page'],
                    'char'      =>$output['char'],
                    'total'     =>$output['total']);

    // Get the values
    $output = xarModAPIFunc(__ADDRESSBOOK__,'user','getsubmitvalues', array ('output'=>$output));

    // Get detailed values from database
    $details = xarModAPIFunc(__ADDRESSBOOK__,'user','getdetailvalues',array('id'=>$output['id']));
    foreach ($details as $key=>$value) {
        $output[$key] = $value;
    }

    $output['authid'] = xarSecGenAuthKey();
    $output['id'] = $output['id'];
    $output['confirmDeleteTEXT'] = xarML(_AB_CONFIRMDELETE);
    $output['buttonDelete'] = xarML(_AB_DELETE);
    $output['buttonCancel'] = xarML(_AB_CANCEL);

    return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));

} // END confirmDelete

?>