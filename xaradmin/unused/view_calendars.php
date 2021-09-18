<?php

function calendar_admin_view_calendars()
{

    // Security check
    if (!xarSecurity::check('Admincalendar')) {
        return;
    }

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSec::genAuthKey();
    $data['default_cal'] = unserialize(xarModVars::get('calendar', 'default_cal'));

    // Return the template variables defined in this function
    $data['calendars'] = xarMod::apiFunc(
        'calendar',
        'user',
        'getall'
    );

    return $data;
}
