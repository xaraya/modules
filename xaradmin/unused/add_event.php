<?php
function calendar_admin_add_event()
{

    // Security check
    if (!xarSecurityCheck('Admincalendar')) return;

    // Generate a one-time authorisation code for this operation
    $data = xarModAPIFunc('calendar', 'admin', 'get_calendars');
    $data['authid'] = xarSecGenAuthKey();
    $data['default_cal'] = unserialize(xarModVars::get('calendar', 'default_cal'));

    // Variables from phpIcalendar config.inc.php
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Create event'));

    //TODO: should I include this stuff? --amoro
/*    $hooks = xarModCallHooks('module', 'modifyconfig', 'calendar',
        array('module' => 'calendar'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    }
*/

    // Return the template variables defined in this function
    return $data;
}
?>
