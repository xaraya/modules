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
                //Set some defaults
                $data['idtype']='';
                $data['class']='1';
                $data['rstate']='0';

                break;

            case 'update':

                list($uid,
                     $regname,
                     $displname,
                     $desc,
                     $idtype,
                     $class,
                     $rstate,
                     $cids) = xarVarCleanFromInput('uid',
                                                   'regname',
                                                   'displname',
                                                   'desc',
                                                   'idtype',
                                                   'class',
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
                                    array('uid' => $uid,
                                          'regname' => $regname,
                                          'displname' => $displname,
                                          'desc' => $desc,
                                          'certified' => '1',
                                          'type' => $idtype,
                                          'class' => $class,
                                          'rstate'=> $rstate,
                                          'cids' => $cids));
                if ($newrid==false) {
                    if (xarCurrentErrorType() == XAR_SYSTEM_EXCEPTION) {
                        return; // throw back
                    }
                    $reason = xarExceptionValue();
                    if (!empty($reason)) {
                       $data['message'] = substr(strrchr($reason->toString(), '|'), 1);
                    }
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

?>
