<?php
/**
 * Modify a Note
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 */
/**
 * Modify a note
 * 
 * @param $rnid ID
 * 
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_admin_modifynote()
{
    // Security Check
    if(!xarSecurityCheck('EditRelease')) return;

    if (!xarVarFetch('rnid', 'id', $rnid)) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

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
            $data['regname'] = $id['regname'];
            $data['username'] = $user['name'];
            $data['changelogf'] = nl2br($data['changelog']);
            $data['notesf'] = nl2br($data['notes']);
            $data['authid'] = xarSecGenAuthKey();
            if ($data['type']==0) {
              $data['idtype']='Module';
            }else {
              $dadta['idtype']='Theme';
            }

            break;
        
        case 'update':

            list($rid,
                 $regname,
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
                 $notes,
                 $rstate) = xarVarCleanFromInput('rid',
                                                'regname',
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
                                                'notes',
                                                'rstate');

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
                                      'approved'    => $approved,
                                      'rstate'      => $rstate))) return;


            xarResponseRedirect(xarModURL('release', 'admin', 'viewnotes'));

            return true;

            break;
    }   
    
    return $data;
}

?>
