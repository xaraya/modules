<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: John Cox via phpMailer Team
// Purpose of file: srtandard mail output
// ----------------------------------------------------------------------

function release_adminapi_getmenulinks()
{
    if (xarSecAuthAction(0, 'release::', '::', ACCESS_EDIT)) {
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'viewnotes'),
                             'title' => xarML('View Release Notifications'),
                             'label' => xarML('View Notifications'));

     }

    if (xarSecAuthAction(0, 'release::', '::', ACCESS_ADMIN)) {
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'addcore'),
                             'title' => xarML('Add Core Notifications'),
                             'label' => xarML('View Notifications'));

     }

    if (xarSecAuthAction(0, 'release::', '::', ACCESS_EDIT)) {
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'viewdocs'),
                             'title' => xarML('View Release Docs'),
                             'label' => xarML('View Documentation'));

     }

    if (xarSecAuthAction(0, 'release::', '::', ACCESS_ADD)) {
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'adddocs'),
                             'title' => xarML('Add Release Docs'),
                             'label' => xarML('Add Documentation'));

     }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>