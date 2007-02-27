<?php
/**
 * AddressBook admin functions
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @link http://xaraya.com/index.php/release/66417.html
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * Main admin function
 * Redirect to modifyconfig
 * @return bool true on success of with redirect
 */
function addressbook_admin_main()
{
    if(!xarSecurityCheck('AdminAddressBook')) {
        return;
    }
    // success
    xarResponseRedirect(xarModURL('addressbook','admin','modifyconfig'));
    return true;
}
?>
