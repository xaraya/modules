<?php

function release_user_modifyid()
{
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    $phase = xarVarCleanFromInput('phase');

    if (empty($phase)){
        $phase = 'modify';
    }

    switch(strtolower($phase)) {

        case 'modify':
        default:
            
            $rid = xarVarCleanFromInput('rid');

            // The user API function is called.
            $data = xarModAPIFunc('release',
                                  'user',
                                  'getid',
                                  array('rid' => $rid));

            if ($data == false) return;
            // The user API function is called.

            $uid = xarUserGetVar('uid');

            if (($data['uid'] == $uid) or (xarSecurityCheck('EditRelease', 0))) {
                $message = '';
            } else {
                $message = xarML('You are not allowed to add a release notification to this module');               
            }
            $stateoptions=array();
            $stateoptions[0] = xarML('Planning');
            $stateoptions[1] = xarML('Alpha');
            $stateoptions[2] = xarML('Beta');
            $stateoptions[3] = xarML('Production/Stable');
            $stateoptions[4] = xarML('Mature');
            $stateoptions[5] = xarML('Inactive');

            foreach ($stateoptions as $key => $value) {
                if ($key==$data['rstate']) {
                    $rstatesel=$stateoptions[$key];
                }
            }
            $data['rstatesel']=$rstatesel;
            $data['stateoptions']=$stateoptions;
            $item['module'] = 'release';
            $hooks = xarModCallHooks('item', 'modify', $rid, $item);
            if (empty($hooks['categories'])) {
                $cathook = '';
            } else {
                $cathook = $hooks['categories'];
            } 
            $data['cathook'] = $cathook;

            $data['authid'] = xarSecGenAuthKey();

            break;
        
        case 'update':

            list($rid,
                 $uid,
                 $regname,
                 $displname,
                 $desc,
                 $certified,
                 $idtype,
                 $class,
                 $rstate,
                 $cids) = xarVarCleanFromInput('rid',
                                               'uid',
                                               'regname',
                                               'displname',
                                               'desc',
                                               'certified',
                                               'idtype',
                                               'class',
                                               'rstate',
                                               'modify_cids');
            
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            // The user API function is called. 
            if (!xarModAPIFunc('release',
                               'user',
                               'updateid',
                                array('rid' => $rid,
                                      'uid' => $uid,
                                      'regname' => $regname,
                                      'displname' => $displname,
                                      'desc' => $desc,
                                      'certified' => $certified,
                                      'type' => $idtype,
                                      'class' => $class,
                                      'rstate' => $rstate,
                                      'cids' => $cids))) return;

            xarResponseRedirect(xarModURL('release', 'user', 'view'));

            return true;

            break;
    }   
    
    return $data;
}

?>