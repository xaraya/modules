<?php
/**
 * Get menu links
 *
 */
function reports_adminapi_getmenulinks() 
{
    $menulinks[] = array('url'   => xarModURL('reports','admin','view_connections'),
                         'label' => xarML('Manage connections'),
                         'title' => xarML('Manage registered report connections'));
    $menulinks[] = array('url'   => xarModURL('reports','admin','view_reports'),
                         'label' => xarML('Manage reports'),
                         'title' => xarML('Manage registered report definitions'));
    $menulinks[] = array('url'   => xarModURL('reports','admin','modify_config'),
                         'label' => xarML('Modify config'),
                         'title' => xarML('Modify reports configuration'));

    return $menulinks;
}
?>