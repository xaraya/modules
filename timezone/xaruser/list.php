<?php
// $Id:$
function timezone_user_list()
{
    xarVarFetch('timezone','str::',$selected_tz,'',XARVAR_NOT_REQUIRED);
    $timezones =& xarModAPIFunc('timezone','user','getTimezoneNames');
    // return the sorted array for display
    return array(
        'timezones'=>$timezones,
        'selected_tz'=>$selected_tz,
        'tz_set'=> !empty($selected_tz)
        );
}
?>