<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the subitems module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function subitems_adminapi_getmenulinks()
{
    $menulinks = array();

// TODO: distinguish between edit/add/delete/admin if necessary
    if (xarSecurityCheck('AdminSubitems', 0)) {
        $menulinks[] = Array('url' => xarModURL('subitems',
                                                'admin',
                                                'ddobjectlink_new'),
                             'title' => xarML('Add Link to DD-Objects'),
                             'label' => xarML('Add Link'));

        $menulinks[] = Array('url' => xarModURL('subitems',
                                                'admin',
                                                'ddobjectlink_view'),
                             'title' => xarML('View Links to DD-Objects'),
                             'label' => xarML('Views Links'));

        $menulinks[] = Array('url' => xarModURL('subitems',
                                                'admin',
                                                'modifyconfig'),
                            'title' => xarML('Modify the configuration for the module'),
                            'label' => xarML('Modify Config'));
    }

    return $menulinks;
}

?>
