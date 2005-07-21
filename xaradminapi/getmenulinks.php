<?php

/*/
 * passes individual menu items to the main admin menu
 *
 * @returns array containing the menulinks for the main menu items.
/*/
function shopping_adminapi_getmenulinks()
{
    $menulinks = array();

    // View Orders Link
    //if (xarSecurityCheck('EditShoppingOrders',0)) {
    //    $menulinks[] = Array('url'   => xarModURL('shopping',
    //                                              'admin',
    //                                              'vieworders'),
    //                         'title' => xarML('View, edit, and delete orders'),
    //                         'label' => xarML('View Orders'));
    //}
    // Add Orders Link
    //if (xarSecurityCheck('AddShoppingOrders',0)) {
    //    $menulinks[] = Array('url'   => xarModURL('shopping',
    //                                              'admin',
    //                                              'addorder'),
    //                         'title' => xarML('Add an order'),
    //                         'label' => xarML('Add Order'));
    //}

    // View Items Link
    if (xarSecurityCheck('EditShoppingItems',0)) {
        $menulinks[] = Array('url'   => xarModURL('shopping',
                                                  'admin',
                                                  'viewitems'),
                             'title' => xarML('View, edit, and delete items'),
                             'label' => xarML('View Items'));
    }
    // Add Item Link
    if (xarSecurityCheck('AddShoppingItems',0)) {
        $menulinks[] = Array('url'   => xarModURL('shopping',
                                                  'admin',
                                                  'additem'),
                             'title' => xarML('Add an item'),
                             'label' => xarML('Add Item'));
    }

    // View & Add Recos Links
    if (xarSecurityCheck('AdminShoppingRecos',0)) {
        $menulinks[] = Array('url'   => xarModURL('shopping',
                                                  'admin',
                                                  'viewrecos'),
                             'title' => xarML('View, edit, and delete recommendations'),
                             'label' => xarML('View Recos'));
    }

    // All Other Links
    if (xarSecurityCheck('AdminShopping',0)) {
        //$menulinks[] = Array('url'   => xarModURL('shopping',
        //                                          'admin',
        //                                          'viewstats'),
        //                     'title' => xarML('View stats about orders, items, and recommendations'),
        //                     'label' => xarML('View Stats'));
        $menulinks[] = Array('url'   => xarModURL('shopping',
                                                  'admin',
                                                  'modifyconfig'),
                             'title' => xarML('Modify the configuration of the shopping module'),
                             'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>
