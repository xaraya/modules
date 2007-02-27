<?php
/**
 * AddressBook user viewAll
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * Export configuration page
 *
 * @return array of menu links
 */
function addressbook_user_export()
{
    $output = array();

    /**
     * Security check first
     */
    if (xarSecurityCheck('AdminAddressBook',0)) {

        xarModAPIFunc('addressbook','user','export');

    }

    return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));

} // END export

?>
