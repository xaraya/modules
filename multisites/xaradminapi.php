<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Sebastien Bernard
// Purpose of file:  Administration of the multisites system.
// based on the templates developped by Jim McDee.
// ----------------------------------------------------------------------

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function multisites_adminapi_getmenulinks()
{

    if (xarSecAuthAction(0, 'multisites::', '::', ACCESS_ADD)) {

        $menulinks[] = Array('url'   => xarModURL('multisites',
                                                   'admin',
                                                   'add'),
                              'title' => xarML('Add a new site into the system'),
                              'label' => xarML('Add'));
    }

    if (xarSecAuthAction(0, 'multisites::', '::', ACCESS_EDIT)) {

        $menulinks[] = Array('url'   => xarModURL('multisites',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('View and Edit Sites'),
                              'label' => xarML('View'));
    }

    if (xarSecAuthAction(0, 'multisites::', '::', ACCESS_ADMIN)) {
        $menulinks[] = Array('url'   => xarModURL('multisites',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration for the Multisites'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}


?>