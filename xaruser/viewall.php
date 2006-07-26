<?php
/**
 * AddressBook user viewAll
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
 * builds an array of menulinks for display in a menu block
 *
 * @return array of menu links
 */
function addressbook_user_viewall()
{

    $output['userIsLoggedIn'] = xarUserIsLoggedIn();
    $output['globalprotect'] = xarModGetVar('addressbook', 'globalprotect');
    $output['userCanViewModule'] = xarSecurityCheck('ReadAddressBook',0);

    /**
     * not sure how this differs from xarSecurityCheck above...
     */
    $output['userCanViewEntries'] = xarModAPIFunc('addressbook','user','checkaccesslevel',array('option'=>'view'));

    /**
     * Get menu values from the input
     */
    $menuValues = xarModAPIFunc('addressbook','user','getmenuvalues');
    foreach ($menuValues as $key=>$value) {
        $output[$key] = $value;
    }

    /**
     * Print the main menu
     */
    $output = xarModAPIFunc('addressbook','user','getmenu',array('output'=>$output));

    // Start Page

    $output = xarModAPIFunc('addressbook','user','getaddresslist',array('output'=>$output));

    return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));

} // END viewall

?>
