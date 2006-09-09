<?php
/**
 * Modify a Note
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
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
            $data = xarModAPIFunc('release', 'user', 'getnote',
                                  array('rnid' => $rnid));

            if ($data == false) return;

            // The user API function is called.
            $id = xarModAPIFunc('release', 'user', 'getid',
                                  array('rid' => $data['rid']));

            if ($id == false) return;

            // The user API function is called.
            $user = xarModAPIFunc('roles', 'user', 'get',
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
           if (!xarVarFetch('rid', 'int:1:', $rid, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('regname', 'str:1:', $regname, NULL, XARVAR_NOT_REQUIRED)) {return;};
           if (!xarVarFetch('version', 'str:1:', $version, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('pricecheck', 'int:1:2', $pricecheck, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('supportcheck', 'int:1:2', $supportcheck, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('democheck', 'int:1:2', $democheck, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('dllink', 'str:1:', $dllink, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('price', 'float', $price, 0, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('demolink', 'str:1:254', $demolink, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('supportlink', 'str:1:254', $supportlink, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('changelog', 'str:1:', $changelog, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('notes', 'str:1:', $notes, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('certified', 'int:1:2', $certified, 1, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('approved', 'int:1:2', $approved, 1, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('rstate', 'int:0:6', $rstate, 0, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('usefeedchecked', 'checkbox', $usefeedchecked, true, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('enotes', 'str:0:', $enotes, '', XARVAR_NOT_REQUIRED)) {return;}
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;
            $usefeed = $usefeedchecked? 1: 0;
            // The user API function is called.
            if (!xarModAPIFunc('release', 'admin', 'updatenote',
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
                                      'rstate'      => $rstate,
                                      'usefeed'     => $usefeed))) return;


            xarResponseRedirect(xarModURL('release', 'admin', 'viewnotes'));

            return true;

            break;
    }   
    
    return $data;
}

?>