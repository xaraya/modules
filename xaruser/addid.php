<?php
/**
 * Add a new extension
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
 * Add an extension and request an ID
 *
 * @param enum phase Phase we are at
 *
 * @return array
 * @author Release module development team
 */
function release_user_addid()
{
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    xarVarFetch('phase', 'enum:add:update', $phase, 'add', XARVAR_NOT_REQUIRED);

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

                if (!xarVarFetch('uid', 'int:1:', $uid, 0, XARVAR_NOT_REQUIRED)) {return;}
                if (!xarVarFetch('regname', 'str:1:', $regname, NULL, XARVAR_NOT_REQUIRED)) {return;};
                if (!xarVarFetch('displname', 'str:1:', $displname, NULL, XARVAR_NOT_REQUIRED)) {return;};
                if (!xarVarFetch('desc', 'str:1:', $desc, NULL, XARVAR_NOT_REQUIRED)) {return;};
                if (!xarVarFetch('idtype', 'int:0:3', $idtype, NULL, XARVAR_NOT_REQUIRED)) {return;};
                if (!xarVarFetch('class', 'int:0:', $class, NULL, XARVAR_NOT_REQUIRED)) {return;};
                if (!xarVarFetch('rstate', 'int:0:6', $rstate, NULL, XARVAR_NOT_REQUIRED)) {return;};
                if (!xarVarFetch('modify_cids', 'list:int:1:', $cids, NULL, XARVAR_NOT_REQUIRED)) {return;};
                
                // Get the UID of the person submitting the module
                $uid = xarUserGetVar('uid');

                // Confirm authorisation code
                if (!xarSecConfirmAuthKey()) return;

                // The user API function is called.
                // TODO: MichelV Why is there a fixed certified here?
                $newrid =  xarModAPIFunc('release', 'user', 'createid',
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
                    $reason = xarCurrentError();
                    if (!empty($reason)) {
                       $data['message'] = substr(strrchr($reason->toString(), '|'), 1);
                    }
                    xarErrorFree();
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