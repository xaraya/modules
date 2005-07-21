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
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                        new SystemException($msg));
        return;
    }

    // Security check
    $input = array();
    $input['calendar'] = $calendar;
    $input['mask'] = 'DeleteCalendars';

/* TODO: security
    if (!xarModAPIFunc('calendar','user','checksecurity',$input)) {
        $msg = xarML('You have no permission to delete item #(1)',
                     xarVarPrepForDisplay($calid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
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
    xarSessionSetVar('statusmsg', xarML('Calendar Deleted'));

    // Return to the original admin view
    $lastview = xarSessionGetVar('Calendar.LastView');
    if (isset($lastview)) {
        $lastviewarray = unserialize($lastview);
        if (!empty($lastviewarray['ptid']) && $lastviewarray['ptid'] == $ptid) {
            extract($lastviewarray);
            xarResponseRedirect(xarModURL('calendar', 'admin', 'view_calendars'));
            return true;
        }
    }

    xarResponseRedirect(xarModURL('calendar', 'admin', 'view_calendars'));

    return true;
}

?>
