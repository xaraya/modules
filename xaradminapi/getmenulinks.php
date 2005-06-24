<?php
function julian_adminapi_getmenulinks()
{

    $menulinks = '';
    /*
    $menulinks[] = Array('url'=>xarModURL('calendar','admin','modifyconfig'),
                         'title'=>xarML('Modify the configuration for Calendar'),
                         'label'=>xarML('Modify Config'));
    $menulinks[] = Array('url'=>xarModURL('calendar','admin','view'),
                         'title'=>xarML('View queued events'),
                         'label'=>xarML('View Queue'));
    */
    
    if (xarSecurityCheck('Adminjulian', 0)) {
        $menulinks[] = Array('url' => xarModURL('julian',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify Config'),
            'label' => xarML('Modify Config'));
    //}
    
    //if (xarSecurityCheck('Adminjulian', 0)) {
        $menulinks[] = Array('url' => xarModURL('julian',
                'admin',
                'modifycategories'),
            'title' => xarML('Modify Categories'),
            'label' => xarML('Modify Categories'));
    }
    return $menulinks;
}
?>
