<?php

function release_adminapi_getmenulinks()
{

    if (xarSecurityCheck('EditRelease', 0)) {
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'addcore'),
                             'title' => xarML('Add Core Notifications'),
                             'label' => xarML('Add Core Release'));

        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'viewids'),
                             'title' => xarML('View Registered IDs on the system'),
                             'label' => xarML('View IDs'));

        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'viewnotes'),
                             'title' => xarML('View Release Notifications'),
                             'label' => xarML('View Notifications'));

        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'viewdocs'),
                             'title' => xarML('View Release Docs'),
                             'label' => xarML('View Documentation'));

        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'modifyconfig'),
                             'title' => xarML('Modify the configuration for the module'),
                             'label' => xarML('Modify Config'));

     }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>