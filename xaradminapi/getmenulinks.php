<?php
function timezone_adminapi_getmenulinks()
{

    $menulinks = array();
    $menulinks[] = Array(
        'url'=>xarModURL('timezone','admin','regenerate'),
        'title'=>xarML('Regenerate a simplified list of timezones and DST rules for the base module'),
        'label'=>xarML('Regenerate Base List')
        );
    $menulinks[] = Array(
        'url'=>xarModURL('timezone','admin','modifyconfig'),
        'title'=>xarML('Modify the configuration for the TimeZone module'),
        'label'=>xarML('Modify Config')
        );
    
     return $menulinks;
}
?>
