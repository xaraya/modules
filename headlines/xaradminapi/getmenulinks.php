<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function headlines_adminapi_getmenulinks()
{

    // Security Check
	if(xarSecurityCheck('AddHeadlines')) {

        $menulinks[] = Array('url'   => xarModURL('headlines',
                                                  'admin',
                                                  'new'),
                              'title' => xarML('Add a new Headline into the system'),
                              'label' => xarML('Add'));
    }

    if(xarSecurityCheck('EditHeadlines')) {

        $menulinks[] = Array('url'   => xarModURL('headlines',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View and Edit Headlines'),
                              'label' => xarML('View'));
    }

    if(xarSecurityCheck('AdminHeadlines')) {

        $menulinks[] = Array('url'   => xarModURL('headlines',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Edit the Headlines Configuration'),
                              'label' => xarML('Modify Config'));
    }


    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
