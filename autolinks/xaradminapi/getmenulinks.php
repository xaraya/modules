<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function autolinks_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('EditAutolinks',0)) {

        $menulinks[] = Array('url'   => xarModURL('autolinks',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('View and Edit Autolinks'),
                              'label' => xarML('View Links'));
    }

    // Security Check
    if (xarSecurityCheck('AddAutolinks',0)) {

        $menulinks[] = Array('url'   => xarModURL('autolinks',
                                                   'admin',
                                                   'new'),
                              'title' => xarML('Add a new Autolink into the system'),
                              'label' => xarML('Add Link'));
    }

    // Security Check
    if (xarSecurityCheck('EditAutolinks',0)) {

        $menulinks[] = Array('url'   => xarModURL('autolinks',
                                                   'admin',
                                                   'viewtype'),
                              'title' => xarML('View and Edit Autolink Types'),
                              'label' => xarML('View Types'));
    }

    // TODO: AddAutolinksTypes ?
    // Security Check
    if (xarSecurityCheck('AddAutolinks',0)) {

        $menulinks[] = Array('url'   => xarModURL('autolinks',
                                                   'admin',
                                                   'newtype'),
                              'title' => xarML('Add a new Autolink Type into the system'),
                              'label' => xarML('Add Type'));
    }

    // Security Check
    if (xarSecurityCheck('AdminAutolinks',0)) {
        $menulinks[] = Array('url'   => xarModURL('autolinks',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration for the Autolinks'),
                              'label' => xarML('Modify Config'));
    }

    // Security Check
    if (xarSecurityCheck('AdminAutolinks', 0)) {
        $menulinks[] = Array(
            'url'   => xarModURL('autolinks', 'util', 'main'),
            'title' => xarML('Autolink utilities'),
            'label' => xarML('Utlilities')
        );
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>