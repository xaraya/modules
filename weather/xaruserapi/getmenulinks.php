<?php
function &weather_userapi_getmenulinks()
{

    xarVarFetch('xwloc','str::',$loc,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('xwunits','str::',$units,null,XARVAR_NOT_REQUIRED);
    
    $menulinks = '';
    
    /*
    $menulinks[] = Array(
        'url'=>xarModURL('weather','user','cc',array('xwloc'=>$loc,'xwunits'=>$units)),
        'title'=>xarML('Current Conditions'),
        'label'=>xarML('Current Conditions')
        );
    
    $menulinks[] = Array(
        'url'=>xarModURL('weather','user','ccdetails',array('xwloc'=>$loc,'xwunits'=>$units)),
        'title'=>xarML('Detailed Forecast'),
        'label'=>xarML('Detailed Forecast')
        );
        
    $menulinks[] = Array(
        'url'=>xarModURL('weather','user','extforecast',array('xwloc'=>$loc,'xwunits'=>$units)),
        'title'=>xarML('Extended Forecast'),
        'label'=>xarML('Extended Forecast')
        );
    */
    $menulinks[] = Array(
        'url'=>xarModURL('weather','user','search',array('xwloc'=>$loc,'xwunits'=>$units)),
        'title'=>xarML('Search Locations'),
        'label'=>xarML('Search Locations')
        );
        
    $menulinks[] = Array(
        'url'=>xarModURL('weather','user','modifyconfig',array('xwloc'=>$loc,'xwunits'=>$units)),
        'title'=>xarML('Modify Config'),
        'label'=>xarML('Modify Config')
        );
   
   return $menulinks;
}
?>