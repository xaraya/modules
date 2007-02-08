<?php
/**
 * Get menu links
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * This function will create the links that are shown in the admin menu
 * @author the Dyn_Example module development team
 * @return array The array contains the menulinks for the main menu items.
 */
function dyn_example_adminapi_getmenulinks()
{
    $menulinks = array();
    // Add a security check, so only admins can see this link
    // Hide the possible error

    if (xarSecurityCheck('AddDynExample',0)) {

        $menulinks[] = Array('url'   => xarModURL('dyn_example',
                                                   'admin',
                                                   'new'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Adds a new item to system.'),
                              'label' => xarML('Add Item'));
    }

    if (xarSecurityCheck('EditDynExample',0)) {

        $menulinks[] = Array('url'   => xarModURL('dyn_example',
                                                   'admin',
                                                   'view'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('View all example items that have been added.'),
                              'label' => xarML('List Items'));
    }

    if (xarSecurityCheck('AdminDynExample',0)) {
        // Add a link to the module's configuration.
        // We place this link last in the list so have a similar menu for all modules
        $menulinks[] = Array('url'   => xarModURL('dyn_example',
                                                   'admin',
                                                   'modifyconfig'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Modify the configuration for the module'),
                              'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>
