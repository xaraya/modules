<?php
/**
 * Modify an ID
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
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
    if (!xarSecurity::check('EditRelease')) {
        return;
    }
    if (!xarVar::fetch('phase', 'str:0:', $phase, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('eid', 'int:1:', $eid, null, xarVar::NOT_REQUIRED)) {
        return;
    }

    $data = xarMod::apiFunc('release', 'user', 'getid', array('eid' => $eid));

    if ($data == false) {
        return;
    }

    if (empty($phase)) {
        $phase = 'modify';
    }
    $exttypes = xarMod::apiFunc('release', 'user', 'getexttypes');
    $data['exttypes']=$exttypes;
    $rid = $data['rid'];
    switch (strtolower($phase)) {

        case 'modify':
        default:

            // The user API function is called.

            $uid = xarUser::getVar('id');
            $memberstring = '';
            $members=trim($data['members']);
            if (isset($members) && !empty($members)) {
                $memberdata = unserialize($members);
                if (count($memberdata)>0) {
                    foreach ($memberdata as $k => $v) {
                        $memberlist[] = xarUser::getVar('uname', $v);
                    }
                }
                $memberstring='';
                foreach ($memberlist as $key=>$uname) {
                    if ($key == 0) {
                        $memberstring = $uname;
                    } else {
                        $memberstring .=','.$uname;
                    }
                }
            }

            $data['memberlist']=$memberstring;
            $openproj = $data['openproj'];
            $data['openproj'] = isset($openproj) && $openproj>0 ? 1:0;
            if (($data['uid'] == $uid) or (xarSecurity::check('EditRelease', 0))) {
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
            $hooks = xarModHooks::call('item', 'modify', $eid, $item);
            if (empty($hooks['categories'])) {
                $cathook = '';
            } else {
                $cathook = $hooks['categories'];
            }
            $data['cathook'] = $cathook;
            $data['authid'] = xarSec::genAuthKey();

            break;

        case 'update':
            if (!xarVar::fetch('rid', 'int:1:', $rid, null, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('uid', 'int:1:', $uid, null, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('regname', 'str:1:', $regname, '', xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('displname', 'str:1:', $displname, '', xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('desc', 'str:0:', $desc, '', xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('certified', 'int:0:1', $certified, 0, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('exttype', 'int:0:', $exttype, null, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('class', 'int:0:', $class, 0, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('rstate', 'int:0:', $rstate, 0, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('newmembers', 'str:0:', $newmembers, '', xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('scmlink', 'str:0:', $scmlink, '', xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('openproj', 'checkbox', $openproj, false, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('cids', 'str:0:', $cids, '', xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('modifyreferer', 'str:0:', $modifyreferer, '', xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('return_url', 'str:0:', $return_url, '', xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('eid', 'int:1:', $eid, null, xarVar::NOT_REQUIRED)) {
                return;
            }
            // Confirm authorisation code
            if (!xarSec::confirmAuthKey()) {
                return;
            }
            $existingmembers = $data['members'];
            $openproj = isset($openproj)? 1:0;
            $memberslist=array();
            if (!empty($newmembers)) {
                $newmemberlist = explode(',', $newmembers);

                foreach ($newmemberlist as $k=>$v) {
                    $userRole = xarMod::apiFunc(
                        'roles',
                        'user',
                        'get',
                        array('uname' => trim($v))
                    );
                    if (is_array($userRole)) {
                        $memberslist[]=$userRole['uid'];
                    }
                }

                $members = serialize($memberslist);
            } else {
                $members = '';
            }

            // The user API function is called.
            if (!xarMod::apiFunc(
                'release',
                'user',
                'updateid',
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
                                      'cids'      => $cids)
            )) {
                return;
            }

                xarController::redirect(xarController::URL('release', 'user', 'display', array('eid'=>$eid)));
          return true;

            break;
    }

    return $data;
}
