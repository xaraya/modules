<?php

/**
 * delete calendar from database
 */
function calendar_admin_delete_calendar()
{
    // Get parameters
    if (!xarVar::fetch('calid', 'id', $calid)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'checkbox', $confirm, false, xarVar::NOT_REQUIRED)) {
        return;
    }

    // Get calendar information
    $calendar = xarMod::apiFunc(
        'calendar',
        'user',
        'get',
        array('calid' => $calid)
    );
    if (!isset($calendar) || $calendar == false) {
        $msg = xarML(
            'Unable to find #(1) item #(2)',
            'Calendar',
            xarVar::prepForDisplay($calid)
        );
        throw new Exception($msg);
    }

    // Security check
    $input = array();
    $input['calendar'] = $calendar;
    $input['mask'] = 'DeleteCalendars';

    /* TODO: security
        if (!xarMod::apiFunc('calendar','user','checksecurity',$input)) {
            $msg = xarML('You have no permission to delete item #(1)',
                         xarVar::prepForDisplay($calid));
            throw new Exception($msg);
        }
    */
    // Check for confirmation
    if (!$confirm) {
        $data = array();

        // Specify for which item you want confirmation
        $data['calid'] = $calid;

        // Use articles user GUI function (not API) for preview
        if (!xarMod::load('calendar', 'user')) {
            return;
        }
        $data['preview'] = xarMod::guiFunc(
            'calendar',
            'user',
            'display',
            array('calid' => $calid)
        );

        // Add some other data you'll want to display in the template
        $data['confirmtext'] = xarML('Confirm deleting this calendar');
        $data['confirmlabel'] = xarML('Confirm');

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSec::genAuthKey();

        // Return the template variables defined in this function
        return $data;
    }

    // Confirmation present
    if (!xarSec::confirmAuthKey()) {
        return;
    }

    // Pass to API
    if (!xarMod::apiFunc(
        'calendar',
        'admin',
        'delete_calendar',
        array('calid' => $calid)
    )) {
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
            xarController::redirect(xarController::URL('calendar', 'admin', 'view_calendars'));
            return true;
        }
    }

    xarController::redirect(xarController::URL('calendar', 'admin', 'view_calendars'));

    return true;
}
