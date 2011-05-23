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
    if (xarSecurityCheck('ModerateXarpagesPage', 0)) {
        $menulinks[] = array(
            'url'   => xarModURL('xarpages', 'admin', 'viewpages'),
            'title' => xarML('View pages'),
            'label' => xarML('View pages')
        );
    }

    if (xarSecurityCheck('AddXarpagesPage', 0)) {
        $menulinks[] = array(
            'url'   => xarModURL('xarpages', 'admin', 'new'),
            'title' => xarML('Add a new page'),
            'label' => xarML('Add a page')
        );
    }

    if (xarSecurityCheck('EditXarpagesPagetype', 0)) {
        $menulinks[] = array(
            'url'   => xarModURL('xarpages', 'admin', 'viewtypes'),
            'title' => xarML('View page types'),
            'label' => xarML('View page types')
        );
    }

    if (xarSecurityCheck('AdminXarpagesPagetype', 0)) {
        $menulinks[] = array(
            'url'   => xarModURL('xarpages', 'admin', 'newtype'),
            'title' => xarML('Add a page type'),
            'label' => xarML('Add a page type')
        );
    }

    if (xarSecurityCheck('AdminXarpagesPage', 0)) {
        $menulinks[] = array(
            'url'   => xarModURL('xarpages', 'admin', 'modifyconfig'),
            'title' => xarML('Configuration'),
            'label' => xarML('Configuration')
        );
    }

    return $menulinks;
}

?>