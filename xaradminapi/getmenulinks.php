<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Events module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function events_adminapi_getmenulinks()
{
    // First we need to do a security check to ensure that we only return menu items
    // that we are suppose to see.  It will be important to add for each menu item that
    // you want to filter.  No sense in someone seeing a menu link that they have no access
    // to edit.  Notice that we are checking to see that the user has permissions, and
    // not that he/she doesn't.

// Security Check
    if (xarSecurityCheck('AddEvents',0)) {

    // The main menu will look for this array and return it for a tree view of the module
    // We are just looking for three items in the array, the url, which we need to use the
    // xarModURL function, the title of the link, which will display a tool tip for the
    // module url, in order to keep the label short, and finally the exact label for the
    // function that we are displaying.

        $menulinks[] = Array('url'   => xarModURL('events',
                                                   'admin',
                                                   'new'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Adds a new event to the system.'),
                              'label' => xarML('Add Event'));
    }

// Security Check
    if (xarSecurityCheck('EditEvents',0)) {

    // We do the same for each new menu item that we want to add to our admin panels.
    // This creates the tree view for each item.  Obviously, we don't need to add every
    // function, but we do need to have a way to navigate through the module.

        $menulinks[] = Array('url'   => xarModURL('events',
                                                   'admin',
                                                   'view'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('View all events that have been added.'),
                              'label' => xarML('View Events'));
    }

// Security Check
    if (xarSecurityCheck('AdminEvents',0)) {

    // We do the same for each new menu item that we want to add to our admin panels.
    // This creates the tree view for each item.  Obviously, we don't need to add every
    // function, but we do need to have a way to navigate through the module.

        $menulinks[] = Array('url'   => xarModURL('events',
                                                   'admin',
                                                   'modifyconfig'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Modify the configuration for the module'),
                              'label' => xarML('Modify Config'));
    }

    // If we return nothing, then we need to tell PHP this, in order to avoid an ugly
    // E_ALL error.
    if (empty($menulinks)){
        $menulinks = '';
    }

    // The final thing that we need to do in this function is return the values back
    // to the main menu for display.

    return $menulinks;
}

?>