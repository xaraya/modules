<?php

function release_userapi_getmenulinks()
{
    if (xarSecurityCheck('OverviewRelease', 0)) {
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'viewids'),
                             'title' => xarML('View all theme and module IDs'),
                             'label' => xarML('View Registration'));
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'viewnotes'),
                             'title' => xarML('View all theme and module releases'),
                             'label' => xarML('Recent Releases'));
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'addid'),
                             'title' => xarML('Add a module or theme ID so it will not be duplicated'),
                             'label' => xarML('Add Registration'));
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'addnotes'),
                             'title' => xarML('Add a module or theme release note'),
                             'label' => xarML('Add Release Notes'));
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'adddocs'),
                             'title' => xarML('Add module or theme documentation'),
                             'label' => xarML('Add Documentation'));

    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>