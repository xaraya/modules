<?php

function release_user_addid()
{
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    $phase = xarVarCleanFromInput('phase');

    if (empty($phase)){
        $phase = 'add';
    }

    if (xarUserIsLoggedIn()){
        switch(strtolower($phase)) {

            case 'add':
            default:

                $data['uid'] = xarUserGetVar('uid');
                $data['authid'] = xarSecGenAuthKey();

                break;
            
            case 'update':

                list($rid,
                     $uid,
                     $name,
                     $desc,
                     $idtype) = xarVarCleanFromInput('rid',
                                                     'uid',
                                                     'name',
                                                     'desc',
                                                     'idtype');
                
                // Get the UID of the person submitting the module
                $uid = xarUserGetVar('uid');

                // Confirm authorisation code
                if (!xarSecConfirmAuthKey()) return;

                // The user API function is called. 
                if (!xarModAPIFunc('release',
                                   'user',
                                   'createid',
                                    array('rid' => $rid,
                                          'uid' => $uid,
                                          'name' => $name,
                                          'desc' => $desc,
                                          'certified' => '1',
                                          'type' => $idtype))) return;

                xarResponseRedirect(xarModURL('release', 'user', 'viewids'));

                return true;

                break;
        }
    } else {
        $data['message'] = xarML('You Must Be Logged In to Assign an ID');
    }
    
    return $data;
}

// Begin Release Data Portion

?>