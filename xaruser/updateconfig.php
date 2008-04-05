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
function weather_user_updateconfig()
{
    // grab the following form variables
    xarVarFetch('default_location','str::',$default_location,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('units','str::',$units,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('extdays','int::',$extdays,null,XARVAR_NOT_REQUIRED);
    
    
    if(isset($default_location)) {
        xarModUserVars::set('weather','default_location',$default_location);
    }
    
    if(isset($units)) {
        xarModUserVars::set('weather','units',$units);
    }
    
    if(isset($extdays)) {
        xarModUserVars::set('weather','extdays',$extdays);
    }
    
    xarResponseRedirect(xarModURL('weather','user','modifyconfig'));
}
?>