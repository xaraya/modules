<?php
function &weather_userapi_getmenulinks()
{

    $menulinks = '';
    
    $menulinks[] = Array(
        'url'=>xarModURL('weather','user','cc'),
        'title'=>xarML('Current Conditions'),
        'label'=>xarML('Current Conditions')
        );
    
    $menulinks[] = Array(
        'url'=>xarModURL('weather','user','ccdetails'),
        'title'=>xarML('Detailed Forecast'),
        'label'=>xarML('Detailed Forecast')
        );
        
    $menulinks[] = Array(
        'url'=>xarModURL('weather','user','extforecast'),
        'title'=>xarML('Extended Forecast'),
        'label'=>xarML('Extended Forecast')
        );
    
    $menulinks[] = Array(
        'url'=>xarModURL('weather','user','search'),
        'title'=>xarML('Search Locations'),
        'label'=>xarML('Search Locations')
        );
        
    $menulinks[] = Array(
        'url'=>xarModURL('weather','user','modifyconfig'),
        'title'=>xarML('Modify Config'),
        'label'=>xarML('Modify Config')
        );
   
   return $menulinks;
}
?>