<?php

/**
 * delete calendar from database
 */
function calendar_admin_delete_calendar()
{
    // Get parameters
    if (!xarVarFetch('calid', 'id', $calid)) return;
    if (!xarVarFetch('confirm', 'checkbox', $confirm, false, XARVAR_NOT_REQUIRED)) return;

    // Get calendar information
    $calendar = xarModAPIFunc('calendar',
                             'user',
                             'get',
                             array('calid' => $calid));
    if (!isset($calendar) || $calendar == false) {
        $msg = xarML('Unable to find #(1) item #(2)',
                     'Calendar', xarVarPrepForDisplay($calid));
        throw new Exception($msg);
    }

    // Security check
    $input = array();
    $input['calendar'] = $calendar;
    $input['mask'] = 'DeleteCalendars';

/* TODO: security
    if (!xarModAPIFunc('calendar','user','checksecurity',$input)) {
        $msg = xarML('You have no permission to delete item #(1)',
                     xarVarPrepForDisplay($calid));
        throw new Exception($msg);
    }
*/
    // Check for confirmation
    if (!$confirm) {
        $data = array();

        // Specify for which item you want confirmation
        $data['calid'] = $calid;

        // Use articles user GUI function (not API) for preview
        if (!xarModLoad('calendar','user')) return;
        $data['preview'] = xarModFunc('calendar', 'user', 'display',
                                      array('calid' => $calid));

        // Add some other data you'll want to display in the template
        $data['confirmtext'] = xarML('Confirm deleting this calendar');
        $data['confirmlabel'] = xarML('Confirm');

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

        // Return the template variables defined in this function
        return $data;
    }

    // Confirmation present
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    if (!xarModAPIFunc('calendar',
                     'admin',
                     'delete_calendar',
                     array('calid' => $calid))) {
        return;
    }

    // Success
    xarSession::setVar('statusmsg', xarML('Calendar Deleted'));

    // Return to the original admin view
    $lastview = xarSession::getVar('Calendar.LastView');
    if (isset($lastview)) {
        $lastviewarray = unserialize($lastview);
        if (!empty($lastviewarray['ptid']) && $lastviewarray['ptid'] == $ptid) {
            extract($lastviewarray);
            xarResponse::Redirect(xarModURL('calendar', 'admin', 'view_calendars'));
            return true;
        }
    }

    xarResponse::Redirect(xarModURL('calendar', 'admin', 'view_calendars'));

    return true;
}

?>
