<?php
/**
 * File: $Id: getmenulinks.php,v 1.3 2003/07/09 00:12:06 garrett Exp $
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
function AddressBook_adminapi_getmenulinks()
{
    // First we need to do a security check to ensure that we only return menu items
    // that we are suppose to see.  It will be important to add for each menu item that
    // you want to filter.  No sense in someone seeing a menu link that they have no access
    // to edit.  Notice that we are checking to see that the user has permissions, and
    // not that he/she doesn't.

// Security Check
    if (xarSecurityCheck('AdminAddressBook',0)) {

    // The main menu will look for this array and return it for a tree view of the module
    // We are just looking for three items in the array, the url, which we need to use the
    // xarModURL function, the title of the link, which will display a tool tip for the
    // module url, in order to keep the label short, and finally the exact label for the
    // function that we are displaying.

        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                   'admin',
                                                   'modifyconfig'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Modify the settings for the module.'),
                              'label' => xarML('Modify Config'));
    }

// Security Check
    if (xarSecurityCheck('ModifyCategories',0)) {

    // We do the same for each new menu item that we want to add to our admin panels.
    // This creates the tree view for each item.  Obviously, we don't need to add every
    // function, but we do need to have a way to navigate through the module.

        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                   'admin',
                                                   'modifycategories'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Modify categories for the module.'),
                              'label' => xarML('Categories'));
    }

// Security Check
    if (xarSecurityCheck('ModifyLabels',0)) {

    // We do the same for each new menu item that we want to add to our admin panels.
    // This creates the tree view for each item.  Obviously, we don't need to add every
    // function, but we do need to have a way to navigate through the module.

        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                   'admin',
                                                   'modifylabels'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Modify module labels used to describe data fields'),
                              'label' => xarML('Contact Labels'));
    }

    if (xarSecurityCheck('ModifyPrefixes',0)) {

    // We do the same for each new menu item that we want to add to our admin panels.
    // This creates the tree view for each item.  Obviously, we don't need to add every
    // function, but we do need to have a way to navigate through the module.

        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                  'admin',
                                                  'modifyprefixes'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Modify contact prefix labels (Mr. / Mrs. / etc.)'),
                              'label' => xarML('Name Prefixes'));
    }

    if (xarSecurityCheck('ModifyCustomFields',0)) {

    // We do the same for each new menu item that we want to add to our admin panels.
    // This creates the tree view for each item.  Obviously, we don't need to add every
    // function, but we do need to have a way to navigate through the module.

        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                  'admin',
                                                  'modifycustomfields'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Modify custom fields'),
                              'label' => xarML('Custom Fields'));
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