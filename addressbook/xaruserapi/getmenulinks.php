<?php
/**
 * File: $Id: getmenulinks.php,v 1.1 2003/07/02 07:08:39 garrett Exp $
 *
 * AddressBook user getMenuLinks
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
function AddressBook_userapi_getmenulinks()
{
    if (xarSecurityCheck('EditAddressBook',0)) {

    // We do the same for each new menu item that we want to add to our admin panels.
    // This creates the tree view for each item.  Obviously, we don't need to add every
    // function, but we do need to have a way to navigate through the module.

        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                   'user',
                                                   'insertedit'),
                              'title' => xarML('Add a new address'),
                              'label' => xarML('New Address'));
    }

    if (xarSecurityCheck('ViewAddressBook',0)) {

    // We do the same for each new menu item that we want to add to our admin panels.
    // This creates the tree view for each item.  Obviously, we don't need to add every
    // function, but we do need to have a way to navigate through the module.

        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                   'user',
                                                   'viewall'),
                              'title' => xarML('View address book entries'),
                              'label' => xarML('View Addresses'));
    }

    return $menulinks;

} // END getMenuLinks

?>