<?php
function calendar_admin_add_calendars()
{

    // Security check
//    if (!xarSecurity::check('AddCalendar',0,'Calendar')) return;
    if (!xarVar::fetch('calid', 'int:0:', $calid, '0', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('calname', 'str', $calname, '', xarVar::NOT_REQUIRED)) {
        return;
    }

    $data = xarMod::apiFunc('calendar', 'admin', 'get_calendars');

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSec::genAuthKey();
    $data['default_cal'] = unserialize(xarModVars::get('calendar', 'default_cal'));
    $data['addbutton'] = xarVar::prepForDisplay(xarML('Add calendar'));
    $data['message'] = xarVar::prepForDisplay(xarML('Created calendar with name "#(1)", ID #(2)', $calname, $calid));
    $data['calid'] = $calid;
    return $data;
}
