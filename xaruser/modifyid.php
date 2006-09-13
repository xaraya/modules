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
 * @param $rid
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_user_modifyid($args)
{
    extract($args);
    // Security Check
    if(!xarSecurityCheck('EditRelease')) return;
    if (!xarVarFetch('phase', 'str:0:', $phase, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('eid', 'int:1:',  $eid, null, XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('release', 'user', 'getid', array('eid' => $eid));

    if ($data == false) return;

    if (empty($phase)){
        $phase = 'modify';
    }
    $exttypes = xarModAPIFunc('release','user','getexttypes');
    $data['exttypes']=$exttypes;
    $rid = $data['rid'];
    switch(strtolower($phase)) {

        case 'modify':
        default:

            // The user API function is called.

            $uid = xarUserGetVar('uid');
            $memberstring = '';
            $members=trim($data['members']);
            if (isset($members) && !empty($members)) {
               $memberdata = unserialize($members);
                if (count($memberdata)>0) {
                    foreach ($memberdata as $k => $v) {
                       $memberlist[]=xarUserGetVar('uname',$v);
                    }
                }
                $memberstring='';
                foreach ($memberlist as $key=>$uname) {
                    if ($key == 0) {
                    $memberstring = $uname;
                    }else{
                    $memberstring .=','.$uname;
                    }
                }
            }

            $data['memberlist']=$memberstring;
            $openproj = $data['openproj'];
            $data['openproj'] = isset($openproj) && $openproj>0 ? 1:0;
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
            $item['itemtype'] = 0;
            $hooks = xarModCallHooks('item', 'modify', $eid, $item);
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
            if (!xarVarFetch('desc',      'str:0:',  $desc, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('certified', 'int:0:1', $certified, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('exttype',   'int:0:',  $exttype, null, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('class',     'int:0:',  $class, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('rstate',    'int:0:',  $rstate, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('newmembers', 'str:0:', $newmembers, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('scmlink',   'str:0:',  $scmlink, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('openproj',   'checkbox',  $openproj, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('cids',      'str:0:',  $cids, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('modifyreferer', 'str:0:',  $modifyreferer, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('return_url', 'str:0:',  $return_url, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('eid',       'int:1:',  $eid, null, XARVAR_NOT_REQUIRED)) return;            
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;
            $existingmembers = $data['members'];
            $openproj = isset($openproj)? 1:0;
            $memberslist=array();
            if (!empty($newmembers)) {
              $newmemberlist = explode(',',$newmembers);

              foreach ($newmemberlist as $k=>$v) {
                  $userRole = xarModAPIFunc('roles',  'user',  'get',
                                       array('uname' => trim($v)));
                  if (is_array($userRole)) {
                      $memberslist[]=$userRole['uid'];
                  }
              }

              $members = serialize($memberslist);
            }else {
                $members = '';
            }

            // The user API function is called.
            if (!xarModAPIFunc('release', 'user','updateid',
                                array('eid'       => $eid,
                                      'rid'       => $rid,
                                      'uid'       => $uid,
                                      'regname'   => $regname,
                                      'displname' => $displname,
                                      'desc'      => $desc,
                                      'certified' => $certified,
                                      'exttype'   => $exttype,
                                      'class'     => $class,
                                      'rstate'    => $rstate,
                                      'members'   => $members,
                                      'scmlink'   => $scmlink,
                                      'openproj'  => $openproj,
                                      'cids'      => $cids))) return;

                xarResponseRedirect(xarModURL('release', 'user', 'display',array('eid'=>$eid)));
          return true;

            break;
    }

    return $data;
}
?>