<?php

function release_user_addid()
{
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    $phase = xarVarCleanFromInput('phase');

    if (empty($phase)){
        $phase = 'add';
    }
    $stateoptions=array();
    $stateoptions[0] = xarML('Planning');
    $stateoptions[1] = xarML('Alpha');
    $stateoptions[2] = xarML('Beta');
    $stateoptions[3] = xarML('Production/Stable');
    $stateoptions[4] = xarML('Mature');
    $stateoptions[5] = xarML('Inactive');
    $data['stateoptions']=$stateoptions;

    if (xarUserIsLoggedIn()){
        switch(strtolower($phase)) {

            case 'add':
            default:
                $data['uid'] = xarUserGetVar('uid');
                $data['authid'] = xarSecGenAuthKey();

                $item['module'] = 'release';
                $hooks = xarModCallHooks('item', 'new', '', $item);
                if (empty($hooks['categories'])) {
                    $cathook = '';
                } else {
                    $cathook = $hooks['categories'];
                } 
                $data['cathook'] = $cathook;

                break;
            
            case 'update':

                list($rid,
                     $uid,
                     $name,
                     $desc,
                     $idtype,
                     $rstate,
                     $cids) = xarVarCleanFromInput('rid',
                                                   'uid',
                                                   'name',
                                                   'desc',
                                                   'idtype',
                                                   'rstate',
                                                   'modify_cids');
                
                // Get the UID of the person submitting the module
                $uid = xarUserGetVar('uid');

                // Confirm authorisation code
                if (!xarSecConfirmAuthKey()) return;

                // The user API function is called. 
                $newrid =  xarModAPIFunc('release',
                                         'user',
                                         'createid',
                                    array('rid' => $rid,
                                          'uid' => $uid,
                                          'name' => $name,
                                          'desc' => $desc,
                                          'certified' => '1',
                                          'type' => $idtype,
                                          'rstate'=> $rstate,
                                          'cids' => $cids));
                if ($newrid==false) {
                    if (xarCurrentErrorType() == XAR_SYSTEM_EXCEPTION) {
                        return; // throw back
                    }
                    $data['message']=xarML('Sorry, that ID is not available');
                    xarExceptionFree();
                    return $data;
                }

                xarResponseRedirect(xarModURL('release', 'user', 'display',array('rid'=>$newrid)));

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
