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
                 $name,
                 $desc,
                 $certified,
                 $idtype,
                 $cids) = xarVarCleanFromInput('rid',
                                               'uid',
                                               'name',
                                               'desc',
                                               'certified',
                                               'idtype',
                                               'modify_cids');
            
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            // The user API function is called. 
            if (!xarModAPIFunc('release',
                               'user',
                               'updateid',
                                array('rid' => $rid,
                                      'uid' => $uid,
                                      'name' => $name,
                                      'desc' => $desc,
                                      'certified' => $certified,
                                      'type' => $idtype,
                                      'cids' => $cids))) return;

            xarResponseRedirect(xarModURL('release', 'user', 'viewids'));

            return true;

            break;
    }   
    
    return $data;
}

?>