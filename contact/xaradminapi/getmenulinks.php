<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the contact module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function contact_adminapi_getmenulinks()
{
    // First we need to do a security check to ensure that we only return menu items
    // that we are suppose to see.  It will be important to add for each menu item that
    // you want to filter.  No sense in someone seeing a menu link that they have no access
    // to edit.  Notice that we are checking to see that the user has permissions, and
    // not that he/she doesn't.
     if(!xarSecurityCheck('ContactAdd')) return;

    // The main menu will look for this array and return it for a tree view of the module
    // We are just looking for three items in the array, the url, which we need to use the
    // xarModURL function, the title of the link, which will display a tool tip for the
    // module url, in order to keep the label short, and finally the exact label for the
    // function that we are displaying.

        $menulinks[] = Array('url'   => xarModURL('contact',
                                                   'admin',
                                                   'new'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Add or Modify Company Information.'),
                              'label' => xarML('Add Company'));

        if(!xarSecurityCheck('ContactAdd')) return;
     // The main menu will look for this array and return it for a tree view of the module
    // We are just looking for three items in the array, the url, which we need to use the
    // xarModURL function, the title of the link, which will display a tool tip for the
    // module url, in order to keep the label short, and finally the exact label for the
    // function that we are displaying.

        $menulinks[] = Array('url'   => xarModURL('contact',
                                                   'admin',
                                                   'add_location'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Add or Modify Location Information.'),
                              'label' => xarML('Add Location'));

       if(!xarSecurityCheck('ContactAdd')) return;
    // The main menu will look for this array and return it for a tree view of the module
    // We are just looking for three items in the array, the url, which we need to use the
    // xarModURL function, the title of the link, which will display a tool tip for the
    // module url, in order to keep the label short, and finally the exact label for the
    // function that we are displaying.

        $menulinks[] = Array('url'   => xarModURL('contact',
                                                   'admin',
                                                   'add_titles'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Add or Modify Title Information.'),
                              'label' => xarML('Add Titles'));


        if(!xarSecurityCheck('ContactAdd')) return;

    // The main menu will look for this array and return it for a tree view of the module
    // We are just looking for three items in the array, the url, which we need to use the
    // xarModURL function, the title of the link, which will display a tool tip for the
    // module url, in order to keep the label short, and finally the exact label for the
    // function that we are displaying.

        $menulinks[] = Array('url'   => xarModURL('contact',
                                                   'admin',
                                                   'add_departments'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Add or Modify Department Information.'),
                              'label' => xarML('Add Department'));


       if(!xarSecurityCheck('ContactAdd')) return;

    // The main menu will look for this array and return it for a tree view of the module
    // We are just looking for three items in the array, the url, which we need to use the
    // xarModURL function, the title of the link, which will display a tool tip for the
    // module url, in order to keep the label short, and finally the exact label for the
    // function that we are displaying.

        $menulinks[] = Array('url'   => xarModURL('contact',
                                                   'admin',
                                                   'add_contact'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Add or Modify Contact Information.'),
                              'label' => xarML('Add Contacts'));


       if(!xarSecurityCheck('ContactAdd')) return;

    // The main menu will look for this array and return it for a tree view of the module
    // We are just looking for three items in the array, the url, which we need to use the
    // xarModURL function, the title of the link, which will display a tool tip for the
    // module url, in order to keep the label short, and finally the exact label for the
    // function that we are displaying.

        $menulinks[] = Array('url'   => xarModURL('contact',
                                                   'admin',
                                                   'add_city'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Add or Modify City Information.'),
                              'label' => xarML('Add City'));


      if(!xarSecurityCheck('ContactAdd')) return;

    // We do the same for each new menu item that we want to add to our admin panels.
    // This creates the tree view for each item.  Obviously, we don't need to add every
    // function, but we do need to have a way to navigate through the module.

        $menulinks[] = Array('url'   => xarModURL('contact',
                                                   'admin',
                                                   'list_contact'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('View all contact items that have been added.'),
                              'label' => xarML('View Contacts'));


         if(!xarSecurityCheck('ContactAdd')) return;

    // We do the same for each new menu item that we want to add to our admin panels.
    // This creates the tree view for each item.  Obviously, we don't need to add every
    // function, but we do need to have a way to navigate through the module.

        $menulinks[] = Array('url'   => xarModURL('contact',
                                                   'admin',
                                                   'modifyconfig'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Modify the configuration for the module'),
                              'label' => xarML('Modify Config'));


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