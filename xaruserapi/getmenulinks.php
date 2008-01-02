<?php
/**
 * Purpose of file
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Weather Module
 * @link http://xaraya.com/index.php/release/662.html
 * @author Weather Module Development Team
 */

/**
 * @author Roger Raymond
 */
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
        
    if (xarUserIsLoggedIn()) {
        $menulinks[] = Array(
            'url'=>xarModURL('weather','user','modifyconfig',array('xwloc'=>$loc,'xwunits'=>$units)),
            'title'=>xarML('Modify Config'),
            'label'=>xarML('Modify Config')
        );
    }

    return $menulinks;
}
?>