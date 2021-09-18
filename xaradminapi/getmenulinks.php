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
    $menulinks = [];

    // Security Check
    if (xarSecurity::check('ModerateXarpagesPage', 0)) {
        $menulinks[] = [
            'url'   => xarController::URL('xarpages', 'admin', 'viewpages'),
            'title' => xarML('View pages'),
            'label' => xarML('View pages'),
        ];
    }

    if (xarSecurity::check('AddXarpagesPage', 0)) {
        $menulinks[] = [
            'url'   => xarController::URL('xarpages', 'admin', 'newpage'),
            'title' => xarML('Add a new page'),
            'label' => xarML('Add a page'),
        ];
    }

    if (xarSecurity::check('EditXarpagesPagetype', 0)) {
        $menulinks[] = [
            'url'   => xarController::URL('xarpages', 'admin', 'viewtypes'),
            'title' => xarML('View page types'),
            'label' => xarML('View page types'),
        ];
    }

    if (xarSecurity::check('AdminXarpagesPagetype', 0)) {
        $menulinks[] = [
            'url'   => xarController::URL('xarpages', 'admin', 'newtype'),
            'title' => xarML('Add a page type'),
            'label' => xarML('Add a page type'),
        ];
    }

    if (xarSecurity::check('AdminXarpagesPage', 0)) {
        $menulinks[] = [
            'url'   => xarController::URL('xarpages', 'admin', 'modifyconfig'),
            'title' => xarML('Configuration'),
            'label' => xarML('Configuration'),
        ];
    }

    return $menulinks;
}
