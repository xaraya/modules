<?php
function &weather_adminapi_getmenulinks()
{

    $menulinks = '';
    
    $menulinks[] = Array(
        'url'=>xarModURL('weather','admin','modifyconfig'),
        'title'=>xarML('Modify the configuration for weather'),
        'label'=>xarML('Modify Config')
        );
    
    return $menulinks;
}
?>
