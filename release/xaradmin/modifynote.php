<?php

function release_admin_modifynote()
{
    // Security Check
    if(!xarSecurityCheck('EditRelease')) return;

    list ($phase,
          $rnid) = xarVarCleanFromInput('phase',
                                        'rnid');

    if (empty($phase)){
        $phase = 'modify';
    }

    switch(strtolower($phase)) {

        case 'modify':
        default:
            
            // The user API function is called.
            $data = xarModAPIFunc('release',
                                  'user',
                                  'getnote',
                                  array('rnid' => $rnid));

            if ($data == false) return;

            // The user API function is called.
            $id = xarModAPIFunc('release',
                                  'user',
                                  'getid',
                                  array('rid' => $data['rid']));

            if ($id == false) return;

            // The user API function is called.
            $user = xarModAPIFunc('roles',
                                  'user',
                                  'get',
                                  array('uid' => $id['uid']));

            if ($id == false) return;


            $data['name'] = $id['name'];
            $data['username'] = $user['name'];
            $data['changelogf'] = nl2br($data['changelog']);
            $data['notesf'] = nl2br($data['notes']);
            $data['authid'] = xarSecGenAuthKey();

            break;
        
        case 'update':

            list($rid,
                 $name,
                 $version,
                 $pricecheck,
                 $supportcheck,
                 $democheck,
                 $dllink,
                 $price,
                 $demolink,
                 $supportlink,
                 $changelog,
                 $enotes,
                 $certified,
                 $approved,
                 $notes) = xarVarCleanFromInput('rid',
                                                'name',
                                                'version',
                                                'pricecheck',
                                                'supportcheck',
                                                'democheck',
                                                'dllink',
                                                'price',
                                                'demolink',
                                                'supportlink',
                                                'changelog',
                                                'enotes',
                                                'certified',
                                                'approved',
                                                'notes');
            
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            // The user API function is called. 
            if (!xarModAPIFunc('release',
                               'admin',
                               'updatenote',
                                array('rid'         => $rid,
                                      'rnid'        => $rnid,
                                      'version'     => $version,
                                      'price'       => $pricecheck,
                                      'supported'   => $supportcheck,
                                      'demo'        => $democheck,
                                      'dllink'      => $dllink,
                                      'priceterms'  => $price,
                                      'demolink'    => $demolink,
                                      'supportlink' => $supportlink,
                                      'changelog'   => $changelog,
                                      'notes'       => $notes,
                                      'enotes'      => $enotes,
                                      'certified'   => $certified,
                                      'approved'    => $approved))) return;


            xarResponseRedirect(xarModURL('release', 'admin', 'viewnotes'));

            return true;

            break;
    }   
    
    return $data;
}

?>