<?php
// $Id:$
function timezone_user_set()
{
    //if(xarVarFetch('timezone','str::',$selected_tz)) {
    //    xarUserSetVar('timezone_tzid',$selected_tz);
    //}
    return xarModFunc('timezone','user','list');
}
?>