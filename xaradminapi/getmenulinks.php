<?php
/**
 * Get menu links
 *
 */
function reports_adminapi_getmenulinks() 
{
    $menulinks[] = array('url'   => xarModURL('reports','admin','view_connections'),
                         'label' => xarML('View connections'),
                         'title' => xarML('List registered report connections'));
    $menulinks[] = array('url'   => xarModURL('reports','admin','view_reports'),
                         'label' => xarML('View reports'),
                         'title' => xarML('List registered report definitions'));
    $menulinks[] = array('url'   => xarModURL('reports','admin','modify_config'),
                         'label' => xarML('Modify config'),
                         'title' => xarML('Modify reports configuration'));

    return $menulinks;
}
?>