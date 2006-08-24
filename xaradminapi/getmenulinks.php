<?php

function xorba_adminapi_getmenulinks($arg)
{
    $menulinks = array();
    $menulinks[] = array
    (
        'url'   => xarModURL('xorba', 'admin','discover'),
        'title' => xarML('Discover objects and methods on a running server'),
        'label' => xarML('Object discovery')
    );
    $menulinks[] = array
    (
        'url'   => xarModURL('xorba', 'admin','manage'),
        'title' => xarML('Client for managing registered servers'),
        'label' => xarML('Management client')
    );
    $menulinks[] = array
    (
        'url'   => xarModURL('xorba', 'admin','servers'),
        'title' => xarML('Manage xorba servers'),
        'label' => xarML('Server registration')
    );
    return $menulinks;
}
?>