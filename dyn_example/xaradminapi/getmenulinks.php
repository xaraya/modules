<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function dyn_example_adminapi_getmenulinks()
{
    $menulinks = array();

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
