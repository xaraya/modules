<?php
function calendar_admin_view_calendars()
{

    // Security check
    if (!xarSecurityCheck('Admincalendar')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['default_cal'] = unserialize(xarModVars::get('calendar', 'default_cal'));

    // Return the template variables defined in this function
    $data['calendars'] = xarModAPIFunc('calendar',
                             'user',
                             'getall');

    return $data;
}
?>
