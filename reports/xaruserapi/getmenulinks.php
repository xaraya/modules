<?php
/**
* Get menu links
 *
 */
function reports_userapi_getmenulinks() 
{
    $menulinks[] = array('url'   => xarModURL('reports','user','view'),
                         'label' => xarML('View reports'),
                         'title' => xarML('Choose a report to work with'));
    
    return $menulinks;
}
?>