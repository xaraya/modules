<?php
/**
 * File: $Id: viewall.php,v 1.1 2003/07/02 07:31:18 garrett Exp $
 *
 * AddressBook user viewAll
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
//  Show all contacts
//=========================================================================
function AddressBook_user_viewall($args) {

    extract($args);

    $data['userIsLoggedIn'] = xarUserIsLoggedIn();
    $data['globalprotect'] = xarModGetVar(__ADDRESSBOOK__, 'globalprotect');
    $data['userCanViewModule'] = xarSecurityCheck('ViewAddressBook',0);

    /**
     * not sure how this differs from xarSecurityCheck above...
     */
    $data['userCanViewEntries'] = xarModAPIFunc(__ADDRESSBOOK__,'user','checkAccessLevel',array('option'=>'view'));

    /**
     * Get menu values from the input
     */
    $menuValues = xarModAPIFunc(__ADDRESSBOOK__,'user','getMenuValues');
    foreach ($menuValues as $key=>$value) {
        $data[$key] = $value;
    }

    /**
     * Print the main menu (could this be a block??)
     */
    $data = xarModAPIFunc(__ADDRESSBOOK__,'user','getMenu',array('data'=>$data));

	// Start Page

    $data = xarModAPIFunc(__ADDRESSBOOK__,'user','getAddressList',array('data'=>$data));

    if (xarExceptionMajor() != XAR_NO_EXCEPTION) {
        // Got an exception
        if ((xarExceptionMajor() == XAR_SYSTEM_EXCEPTION) && !_AB_DEBUG) {
            return; // throw back
        } else {
            // We are going to handle this exception REGARDLESS of the type
            $data['abExceptions'] = xarModAPIFunc(__ADDRESSBOOK__,'user','handleException');
        }
    }

    return $data;

} // END viewall

?>