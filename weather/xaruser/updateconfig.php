<?php
function weather_user_updateconfig()
{
    // grab the following form variables
    xarVarFetch('default_location','str::',$default_location,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('units','str::',$units,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('extdays','int::',$extdays,null,XARVAR_NOT_REQUIRED);
    
    
    if(isset($default_location)) {
        xarModSetUserVar('weather','default_location',$default_location);
    }
    
    if(isset($units)) {
        xarModSetUserVar('weather','units',$units);
    }
    
    if(isset($extdays)) {
        xarModSetUserVar('weather','extdays',$extdays);
    }
    
    xarResponseRedirect(xarModURL('weather','user','modifyconfig'));
}
?>