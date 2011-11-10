<?php
function fulltext_adminapi_getmenulinks($args)
{
    $menulinks[] = array(
        'url' => xarModURL('fulltext','admin','main'),
        'title' => xarML('Fulltext Search Overview'),
        'label' => xarML('Overview'),
        'active' => array('main', 'overview'),
    );

    $menulinks[] = array(
        'url' => xarModURL('fulltext','admin','manage'),
        'title' => xarML('Manage Fulltext Search Options'),
        'label' => xarML('Manage'),
        'active' => array('manage'),
    );

    $menulinks[] = array(
        'url' => xarModURL('fulltext','admin','hooks'),
        'title' => xarML('Manage Fulltext Search Hooks'),
        'label' => xarML('Hooks'),
        'active' => array('hooks'),
    );

    $menulinks[] = array(
        'url' => xarModURL('fulltext','admin','search'),
        'title' => xarML('Fulltext Search'),
        'label' => xarML('Search'),
        'active' => array('search'),
    );

    $menulinks[] = array(
        'url' => xarModURL('fulltext','admin','modifyconfig'),
        'title' => xarML('Fulltext Search Configuration'),
        'label' => xarML('Modify Config'),
        'active' => array('modifyconfig'),
    );

    return $menulinks;
}
?>