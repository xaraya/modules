<?php
function calendar_admin_create_calendars()
{

    // Get parameters
    // TODO HELPNEEDED here: how do I handle this (e.g. missing calname should return a
    // message
    if (!xarVarFetch('add_calendar', 'isset', $add_calendar)) {return;}
    if (!xarVarFetch('calname', 'str:1:', $calname)) {return;}
    if (!xarVarFetch('addtype', 'str:1:', $addtype)) {return;}
    if (!xarVarFetch('location', 'str:1:', $location, '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('uri', 'str:1:', $uri, '', XARVAR_NOT_REQUIRED)) {return;}
    
    // Confirm Auth Key
    if (!xarSecConfirmAuthKey()) {return;}
    
    // Security Check
    // TODO 
//    if(!xarSecurityCheck('AddCalendar', 0, 'Calendar')) {return;}
    
    // Check if module name has already been used.
    $checkname = xarModAPIFunc('calendar', 'user', 'get', array('calname' => $calname));
    if (!empty($checkname)) {
        $msg = xarML('Calendar name "#(1)" already exists. Please go back and enter a
                      different name', $calname);
        throw new Exception($msg);
    }
    
    if ($addtype == 'db')  {    
        $fileuri = 'a';
    } elseif ($addtype == 'file') {
        $fileuri = $location;
    } elseif ($addtype == 'uri')  {
        $fileuri = $uri;
    }    
    
    // Pass to API
    $calid = xarModAPIFunc(
        'calendar', 'admin', 'create_calendars',
            array(  'calname'      => $calname
                   ,'fileuri'     => $fileuri 
                   ,'addtype'     => $addtype
//  TODO: modid, and rolid
//              ,  'mod_id'      => $mod_id
//              ,  'role_id'  => $role_id
                 )
            );

    if (!$calid) return;
    
    // Go on and edit the new instance
    xarResponseRedirect(
            xarModURL('calendar', 'admin', 'add_calendars',array('calid'=>$calid,'calname'=>$calname) )
            );
    
} 
?>
