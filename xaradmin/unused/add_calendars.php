<?php
function calendar_admin_add_calendars()
{

    // Security check
//    if (!xarSecurityCheck('AddCalendar',0,'Calendar')) return;
    if (!xarVarFetch('calid', 'int:0:', $calid, '0', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('calname', 'str', $calname, '', XARVAR_NOT_REQUIRED)) {return;}

    $data = xarModAPIFunc('calendar', 'admin', 'get_calendars');

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['default_cal'] = unserialize(xarModVars::get('calendar', 'default_cal'));
    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add calendar'));
    $data['message'] = xarVarPrepForDisplay(xarML('Created calendar with name "#(1)", ID #(2)',$calname,$calid));
    $data['calid'] = $calid;
    return $data;
}
?>
