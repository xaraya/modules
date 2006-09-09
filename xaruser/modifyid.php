<?php
/**
 * Modify an ID
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
 * Modify an ID by user
 * 
 * @param $rnid 
 * 
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 * @TODO remove legacy xarVarCleanFromInput()
 */
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
            if (!xarVarFetch('rid', 'int:1:', $rid, null, XARVAR_NOT_REQUIRED)) return;
            // The user API function is called.
            $data = xarModAPIFunc('release', 'user', 'getid',
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
            if (!xarVarFetch('rid',       'int:1:',  $rid, null, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('uid',       'int:1:',  $uid, null, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('regname',   'str:1:',  $regname, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('displname', 'str:1:',  $displname, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('desc',      'desc:1:', $desc, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('certified', 'int:0:1', $certified, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('idtype',    'int:0:',  $idtype, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('class',     'int:0:',  $class, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('rstate',    'int:0:',  $rstate, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('cids',      'str:0:',  $cids, '', XARVAR_NOT_REQUIRED)) return;
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            // The user API function is called.
            if (!xarModAPIFunc('release', 'user','updateid',
                                array('rid'       => $rid,
                                      'uid'       => $uid,
                                      'regname'   => $regname,
                                      'displname' => $displname,
                                      'desc'      => $desc,
                                      'certified' => $certified,
                                      'type'      => $idtype,
                                      'class'     => $class,
                                      'rstate'    => $rstate,
                                      'cids'      => $cids))) return;

            xarResponseRedirect(xarModURL('release', 'user', 'view'));

            return true;

            break;
    }

    return $data;
}
?>