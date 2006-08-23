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
    return $menulinks;
}
?>