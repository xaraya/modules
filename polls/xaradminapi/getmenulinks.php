<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function polls_adminapi_getmenulinks()
{

    $data['authid'] = xarSecGenAuthKey();

    if (xarSecurityCheck('AddPolls',0)) {
        $menulinks[] = Array('url'   => xarModURL('polls',
                                                   'admin',
                                                   'new'),
                              'title' => xarML('Create a New Poll'),
                              'label' => xarML('New Poll'));
    }
    if (xarSecurityCheck('EditPolls',0)) {
        $menulinks[] = Array('url'   => xarModURL('polls',
                                                   'admin',
                                                   'list'),
                              'title' => xarML('View a list of previous polls'),
                              'label' => xarML('List Polls'));
    }
    if (xarSecurityCheck('AdminPolls',0)) {
        $menulinks[] = Array('url' => xarModURL('polls',
                                                   'admin',
                                                   "modifyconfig&amp;authid=$data[authid]"),
                              'title' => xarML('Modify Polls configuration'),
                              'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>