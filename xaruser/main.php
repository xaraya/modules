<?php
/**
 * AddressBook user functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */
/**
 * Main user function
 * @return array with redirect URL
 */
function AddressBook_user_main()
{
    $output = xarModFunc('addressbook','user','viewall');

    return xarModAPIFunc('addressbook','util','handleException',array('output'=>$output));
} // END main

?>
