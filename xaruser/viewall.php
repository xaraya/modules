<?php
/**
 * File: $Id: viewall.php,v 1.3 2003/12/22 07:12:50 garrett Exp $
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

/**
 * builds an array of menulinks for display in a menu block
 *
 * @return array of menu links
 */
function addressbook_user_viewall() 
{

    $output['userIsLoggedIn'] = xarUserIsLoggedIn();
    $output['globalprotect'] = xarModGetVar(__ADDRESSBOOK__, 'globalprotect');
    $output['userCanViewModule'] = xarSecurityCheck('ViewAddressBook',0);

    /**
     * not sure how this differs from xarSecurityCheck above...
     */
    $output['userCanViewEntries'] = xarModAPIFunc(__ADDRESSBOOK__,'user','checkaccesslevel',array('option'=>'view'));

    /**
     * Get menu values from the input
     */
    $menuValues = xarModAPIFunc(__ADDRESSBOOK__,'user','getmenuvalues');
    foreach ($menuValues as $key=>$value) {
        $output[$key] = $value;
    }

    /**
     * Print the main menu
     */
    $output = xarModAPIFunc(__ADDRESSBOOK__,'user','getmenu',array('output'=>$output));

    // Start Page

    $output = xarModAPIFunc(__ADDRESSBOOK__,'user','getaddresslist',array('output'=>$output));

    return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));

} // END viewall

?>