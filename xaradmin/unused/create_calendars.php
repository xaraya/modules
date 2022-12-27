<?php

function calendar_admin_create_calendars()
{
    // Get parameters
    // TODO HELPNEEDED here: how do I handle this (e.g. missing calname should return a
    // message
    if (!xarVar::fetch('add_calendar', 'isset', $add_calendar)) {
        return;
    }
    if (!xarVar::fetch('calname', 'str:1:', $calname)) {
        return;
    }
    if (!xarVar::fetch('addtype', 'str:1:', $addtype)) {
        return;
    }
    if (!xarVar::fetch('location', 'str:1:', $location, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('uri', 'str:1:', $uri, '', xarVar::NOT_REQUIRED)) {
        return;
    }

    // Confirm Auth Key
    if (!xarSec::confirmAuthKey()) {
        return;
    }

    // Security Check
    // TODO
//    if(!xarSecurity::check('AddCalendar', 0, 'Calendar')) {return;}

    // Check if module name has already been used.
    $checkname = xarMod::apiFunc('calendar', 'user', 'get', ['calname' => $calname]);
    if (!empty($checkname)) {
        $msg = xarML('Calendar name "#(1)" already exists. Please go back and enter a
                      different name', $calname);
        throw new Exception($msg);
    }

    if ($addtype == 'db') {
        $fileuri = 'a';
    } elseif ($addtype == 'file') {
        $fileuri = $location;
    } elseif ($addtype == 'uri') {
        $fileuri = $uri;
    }

    // Pass to API
    $calid = xarMod::apiFunc(
        'calendar',
        'admin',
        'create_calendars',
        [  'calname'      => $calname,'fileuri'     => $fileuri,'addtype'     => $addtype,
//  TODO: modid, and rolid
//              ,  'mod_id'      => $mod_id
//              ,  'role_id'  => $role_id
                 ]
    );

    if (!$calid) {
        return;
    }

    // Go on and edit the new instance
    xarController::redirect(
        xarController::URL('calendar', 'admin', 'add_calendars', ['calid'=>$calid,'calname'=>$calname])
    );
}
