<?php
/**
 * Add a new extension
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
 * Add an extension and request an ID
 *
 * @param enum phase Phase we are at
 *
 * @return array
 * @author Release module development team
 */
function release_admin_new_extension($args)
{
    if (!xarSecurity::check('AddRelease')) {
        return;
    }

    if (!xarVar::fetch('name', 'str', $name, 'release_extensions', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'bool', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => 'release_extensions']);
    $data['tplmodule'] = 'release';

    if ($data['confirm']) {
        // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
        if (!xarVar::fetch('preview', 'str', $preview, null, xarVar::DONT_SET)) {
            return;
        }

        // Check for a valid confirmation key
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        // Get the data from the form
        $isvalid = $data['object']->checkInput();

        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTpl::module('release', 'admin', 'new_extension', $data);
        } else {
            // Good data: create the item
            $itemid = $data['object']->createItem();

            // Jump to the next page
            xarController::redirect(xarController::URL('release', 'admin', 'view_extensions'));
            return true;
        }
    }
    return $data;
    /*
        extract($args);
        // Security Check
        if(!xarSecurity::check('AddRelease')) return;

        xarVar::fetch('phase', 'enum:add:update', $phase, 'add', xarVar::NOT_REQUIRED);
        xarVar::fetch('msg', 'str', $msg, '', xarVar::NOT_REQUIRED);

        $data['msg']=$msg;
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

        $exttypes = xarMod::apiFunc('release','user','getexttypes'); //extension types

        $data['exttypes']=$exttypes;
        if (xarUser::isLoggedIn()){
            switch(strtolower($phase)) {

                case 'add':
                default:
                    $data['uid'] = xarUser::getVar('id');
                    $data['authid'] = xarSec::genAuthKey();

                    $item['module'] = 'release';
                    $hooks = xarModHooks::call('item', 'new', '', $item);
                    if (empty($hooks['categories'])) {
                        $cathook = '';
                    } else {
                        $cathook = $hooks['categories'];
                    }
                    $data['cathook'] = $cathook;
                    //Set some defaults
                    $data['exttype']=1;
                    $data['class']='1';
                    $data['rstate']='0';
                    $data['openproj']=0;

                    break;

                case 'update':
                    if (!xarVar::fetch('ridno', 'int:1:', $ridno, NULL, xarVar::NOT_REQUIRED)) {return;};
                    if (!xarVar::fetch('uid', 'int:1:', $uid, 0, xarVar::NOT_REQUIRED)) {return;}
                    if (!xarVar::fetch('regname', 'str:1:', $regname, NULL, xarVar::NOT_REQUIRED)) {return;};
                    if (!xarVar::fetch('displname', 'str:1:', $displname, NULL, xarVar::NOT_REQUIRED)) {return;};
                    if (!xarVar::fetch('desc', 'str:1:', $desc, '', xarVar::NOT_REQUIRED)) {return;};
                    if (!xarVar::fetch('exttype', 'int:0:', $exttype, NULL, xarVar::NOT_REQUIRED)) {return;};
                    if (!xarVar::fetch('class', 'int:0:', $class, NULL, xarVar::NOT_REQUIRED)) {return;};
                    if (!xarVar::fetch('rstate', 'int:0:6', $rstate, NULL, xarVar::NOT_REQUIRED)) {return;};
                    if (!xarVar::fetch('members', 'str:0:', $members, '', xarVar::NOT_REQUIRED)) {return;};
                    if (!xarVar::fetch('scmlink', 'str:0:', $scmlink, '', xarVar::NOT_REQUIRED)) {return;};
                    if (!xarVar::fetch('openproj', 'checkbox', $openproj, 0, xarVar::NOT_REQUIRED)) {return;};
                    if (!xarVar::fetch('modify_cids', 'list:int:1:', $cids, NULL, xarVar::NOT_REQUIRED)) {return;};

                    // Get the UID of the person submitting the module
                    $uid = xarUser::getVar('id');
                    $openproj = isset($openproj)? 1:0;
                    // Confirm authorisation code
                    if (!xarSec::confirmAuthKey()) return;
                    if (!isset($ridno)) $ridno = 0;
                    // The user API function is called.
                    $newid =  xarMod::apiFunc('release', 'user', 'createid',
                                        array('ridno'     => $ridno,
                                              'uid'       => $uid,
                                              'regname'   => $regname,
                                              'displname' => $displname,
                                              'desc'      => $desc,
                                              'certified' => '1',
                                              'exttype'   => $exttype,
                                              'class'     => $class,
                                              'rstate'    => $rstate,
                                              'members'   => $members,
                                              'scmlink'   => $scmlink,
                                              'openproj'  => $openproj,
                                              'cids'      => $cids));
                    if ($newid==false) {
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

                    xarController::redirect(xarController::URL('release', 'user', 'display',array('eid'=>$newid)));
                    return true;
                    break;
            }
        } else {
            $data['message'] = xarML('You must be logged in to register an extension');
        }
    */
    return $data;
}
