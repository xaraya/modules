<?php
function timezone_adminapi_getmenulinks()
{

    $menulinks = array();
    $menulinks[] = Array(
        'url'=>xarModURL('timezone','admin','modifyconfig'),
        'title'=>xarML('Modify the configuration for the TimeZone module'),
        'label'=>xarML('Modify Config')
        );
    
     return $menulinks;
}
?>
