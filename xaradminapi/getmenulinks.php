<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */

function xarpages_adminapi_getmenulinks()
{
    $menulinks = array();

    // Security Check
    if (xarSecurityCheck('ModeratePage', 0)) {
        $menulinks[] = array(
            'url'   => xarModURL('xarpages', 'admin', 'viewpages'),
            'title' => xarML('View pages'),
            'label' => xarML('View pages')
        );
    }

    if (xarSecurityCheck('AddPage', 0)) {
        $menulinks[] = array(
            'url'   => xarModURL('xarpages', 'admin', 'newpage'),
            'title' => xarML('Add a new page'),
            'label' => xarML('Add a page')
        );
    }

    if (xarSecurityCheck('EditPagetype', 0)) {
        $menulinks[] = array(
            'url'   => xarModURL('xarpages', 'admin', 'viewtypes'),
            'title' => xarML('View page types'),
            'label' => xarML('View page types')
        );
    }

    if (xarSecurityCheck('AdminPagetype', 0)) {
        $menulinks[] = array(
            'url'   => xarModURL('xarpages', 'admin', 'newtype'),
            'title' => xarML('Add a page type'),
            'label' => xarML('Add a page type')
        );
    }

    if (xarSecurityCheck('AdminPage', 0)) {
        $menulinks[] = array(
            'url'   => xarModURL('xarpages', 'admin', 'modifyconfig'),
            'title' => xarML('Configuration'),
            'label' => xarML('Configuration')
        );
    }

    return $menulinks;
}

?>