<?php
/**
 * File: $Id: getmenulinks.php,v 1.5 2003/12/22 07:12:50 garrett Exp $
 *
 * AddressBook admin getMenuLinks
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
function addressbook_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminAddressBook',0)) {
        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the settings for the module.'),
                              'label' => xarML('Modify Config'));
    }

    if (xarSecurityCheck('AdminAddressBook',0)) {
        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                   'admin',
                                                   'modifycategories'),
                              'title' => xarML('Modify categories for the module.'),
                              'label' => xarML('Categories'));
    }

    if (xarSecurityCheck('AdminAddressBook',0)) {
        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                   'admin',
                                                   'modifylabels'),
                              'title' => xarML('Modify module labels used to describe data fields'),
                              'label' => xarML('Contact Labels'));
    }

    if (xarSecurityCheck('AdminAddressBook',0)) {
        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                  'admin',
                                                  'modifyprefixes'),
                              'title' => xarML('Modify contact prefix labels (Mr. / Mrs. / etc.)'),
                              'label' => xarML('Name Prefixes'));
    }

    if (xarSecurityCheck('AdminAddressBook',0)) {
        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                  'admin',
                                                  'modifycustomfields'),
                              'title' => xarML('Modify custom fields'),
                              'label' => xarML('Custom Fields'));
    }

    if (xarSecurityCheck('AdminAddressBook',0)) {
        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                  'admin',
                                                  'displaydocs'),
                              'title' => xarML('Administrator Documentation'),
                              'label' => xarML('Admin Docs'));
    }

    // If we return nothing, then we need to tell PHP this, in order to avoid an ugly
    // E_ALL error.
    if (empty($menulinks)){
        $menulinks = '';
    }

    // The final thing that we need to do in this function is return the values back
    // to the main menu for display.

    return $menulinks;

} // END getMenuLinks

?>