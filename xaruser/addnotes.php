<?php
/*
 * Add an extension release note
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
 */
function release_user_addnotes($args)
{
    extract($args);
    // Security Check
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }
    xarVar::fetch('phase', 'enum:getmodule:start:getbasics:getdetails:preview:update', $phase, 'getmodule', xarVar::NOT_REQUIRED);
    if (!xarVar::fetch('eid', 'int:1:', $eid, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('rid', 'int:1:', $rid, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('exttype', 'int:1:', $exttype, null, xarVar::NOT_REQUIRED)) {
        return;
    }

    if (isset($eid) && $eid>0) {
        $data = xarMod::apiFunc(
            'release',
            'user',
            'getid',
            ['eid' => $eid]
        );
    }
    if (!isset($eid) && isset($rid) && isset($exttype)) {
        $data = xarMod::apiFunc(
            'release',
            'user',
            'getid',
            ['rid' => $rid, 'exttype' =>$exttype]
        );
    }
    if (isset($data) && !empty($data['regname'])) {
        $rid = $data['rid'];
        $exttype = $data['exttype'];
        $regname = $data['regname'];
        $eid =$data['eid'];
    } else {
        $regname ='';
    }


    $exttypes = xarMod::apiFunc('release', 'user', 'getexttypes'); //extension types
    $data['exttypes']=$exttypes;

    if (empty($phase)) {
        $phase = 'getmodule';
    }
    //Set the stateoptions array for rstate field
    $stateoptions=[];
    $stateoptions[0] = xarML('Planning');
    $stateoptions[1] = xarML('Alpha');
    $stateoptions[2] = xarML('Beta');
    $stateoptions[3] = xarML('Production/Stable');
    $stateoptions[4] = xarML('Mature');
    $stateoptions[5] = xarML('Inactive');
    $data['stateoptions']=$stateoptions;

    switch (strtolower($phase)) {
        case 'getmodule':
        default:
            // First we need to get the extension and extension type that we are adding the release note to.
            if (!isset($m)) {
                $m ='';
            }

            $authid = xarSec::genAuthKey();
            $data = xarTpl::module(
                'release',
                'user',
                'addnote_getmodule',
                ['authid' => $authid,
                      'rid'=>$rid,
                      'regname'=>$regname,
                      'exttype'=>$exttype,
                      'exttypes'=>$exttypes,
                      'message'=>$m, ]
            );

            break;

        case 'start':

            $data = xarMod::apiFunc(
                'release',
                'user',
                'getid',
                ['rid' => $rid, 'exttype' => $exttype]
            );

            $exttypename = array_search($exttype, array_flip($exttypes));

            if (!isset($data['regname']) || empty($data['regname'])) {
                $m = xarML('Sorry, that extension number and extension type combination does not exist');

                xarController::redirect(xarController::URL(
                    'release',
                    'user',
                    'addnotes',
                    ['m'=>$m,'rid'=>$rid,'exttype'=>$exttype,'phase'=>'getmodule']
                ));
            }

            $uid = xarUser::getVar('id');

            if (($data['uid'] == $uid) or (xarSecurity::check('EditRelease', 0))) {
                $message = '';
            } else {
                $message = xarML('You are not allowed to add a release notification to this module');
            }

            //TODO FIX ME!!!
            if (empty($data['regname'])) {
                $message = xarML('There is no assigned ID for your extension.');
            }

            xarTpl::setPageTitle(xarVar::prepForDisplay($data['regname']));

            $authid = xarSec::genAuthKey();
            $data = xarTpl::module(
                'release',
                'user',
                'addnote_start',
                ['eid'       => $data['eid'],
                                                         'rid'       => $data['rid'],
                                                         'regname'   => $data['regname'],
                                                         'displname'=>   $data['displname'],
                                                         'desc'      => $data['desc'],
                                                         'message'   => $message,
                                                         'exttype'  => $exttype,
                                                         'authid'    => $authid, ]
            );
            break;

        case 'getbasics':

           //if (!xarSec::confirmAuthKey()) return;
           $democheck=1;
           $supportcheck=1;
           $pricecheck=1;
            xarTpl::setPageTitle(xarVar::prepForDisplay($regname));

           $authid = xarSec::genAuthKey();
           $data = xarTpl::module('release', 'user', 'addnote_getbasics', ['eid'       => $eid,
                                                                             'rid'      => $rid,
                                                                             'regname'  => $regname,
                                                                             'authid'   => $authid,
                                                                             'democheck' => $democheck,
                                                                             'supportcheck' => $supportcheck,
                                                                             'exttype'  => $exttype,
                                                                             'pricecheck' => $pricecheck, ]);
            break;

        case 'getdetails':

           if (!xarVar::fetch('rid', 'int:1:', $rid, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('eid', 'int:1:', $eid, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('exttype', 'int:1:', $exttype, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('regname', 'str:1:', $regname, null, xarVar::NOT_REQUIRED)) {
               return;
           };
           if (!xarVar::fetch('version', 'str:1:', $version, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('pricecheck', 'int:1:2', $pricecheck, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('supportcheck', 'int:1:2', $supportcheck, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('democheck', 'int:1:2', $democheck, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('usefeedchecked', 'checkbox', $usefeedchecked, false, xarVar::NOT_REQUIRED)) {
               return;
           }
           //if (!xarSec::confirmAuthKey()) return;
           $usefeed = $usefeedchecked ? 1 : 0;
            xarTpl::setPageTitle(xarVar::prepForDisplay($regname));

           $authid = xarSec::genAuthKey();
           $data = xarTpl::module('release', 'user', 'addnote_getdetails', ['eid'          => $eid,
                                                                              'rid'          => $rid,
                                                                              'regname'      => $regname,
                                                                              'authid'       => $authid,
                                                                              'version'      => $version,
                                                                              'pricecheck'   => $pricecheck,
                                                                              'supportcheck' => $supportcheck,
                                                                              'democheck'    => $democheck,
                                                                              'exttype'      => $exttype,
                                                                              'stateoptions' => $stateoptions,
                                                                              'usefeed'      => $usefeed, ]);

            break;

        case 'preview':
           if (!xarVar::fetch('rid', 'int:1:', $rid, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('eid', 'int:1:', $eid, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('exttype', 'int:1:', $exttype, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('regname', 'str:1:', $regname, null, xarVar::NOT_REQUIRED)) {
               return;
           };
           if (!xarVar::fetch('version', 'str:1:', $version, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('pricecheck', 'int:1:2', $pricecheck, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('supportcheck', 'int:1:2', $supportcheck, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('democheck', 'int:1:2', $democheck, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('dllink', 'str:1:', $dllink, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('price', 'str', $price, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('demolink', 'str:1:254', $demolink, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('supportlink', 'str:1:254', $supportlink, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('changelog', 'str', $changelog, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('notes', 'str', $notes, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('rstate', 'int:0:6', $rstate, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('usefeedchecked', 'checkbox', $usefeedchecked, false, xarVar::NOT_REQUIRED)) {
               return;
           }
           $usefeed = $usefeedchecked ? 1 : 0;
           //if (!xarSec::confirmAuthKey()) return;
           //Get some info for the extensions state
           foreach ($stateoptions as $key => $value) {
               if ($key==$rstate) {
                   $extstate=$stateoptions[$key];
               }
           }
           $notesf = nl2br($notes);
           $changelogf = nl2br($changelog);

            xarTpl::setPageTitle(xarVar::prepForDisplay($regname));

           $authid = xarSec::genAuthKey();
           $data = xarTpl::module('release', 'user', 'addnote_preview', ['rid'         => $rid,
                                                                              'eid'         => $eid,
                                                                              'regname'     => $regname,
                                                                              'authid'      => $authid,
                                                                              'version'     => $version,
                                                                              'pricecheck'  => $pricecheck,
                                                                              'supportcheck'=> $supportcheck,
                                                                              'democheck'   => $democheck,
                                                                              'dllink'      => $dllink,
                                                                              'price'       => $price,
                                                                              'demolink'    => $demolink,
                                                                              'supportlink' => $supportlink,
                                                                              'changelog'   => $changelog,
                                                                              'changelogf'  => $changelogf,
                                                                              'notesf'      => $notesf,
                                                                              'notes'       => $notes,
                                                                              'rstate'      => $rstate,
                                                                              'exttype'     => $exttype,
                                                                              'stateoptions'=> $stateoptions,
                                                                              'extstate'     => $extstate,
                                                                              'usefeed'      => $usefeed, ]);



            break;

        case 'update':
           if (!xarVar::fetch('rid', 'int:1:', $rid, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('eid', 'int:1:', $eid, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('exttype', 'int:1:', $exttype, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('regname', 'str:1:', $regname, null, xarVar::NOT_REQUIRED)) {
               return;
           };
           if (!xarVar::fetch('version', 'str:1:', $version, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('pricecheck', 'int:1:2', $pricecheck, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('supportcheck', 'int:1:2', $supportcheck, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('democheck', 'int:1:2', $democheck, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('dllink', 'str:1:', $dllink, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('price', 'float', $price, 0, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('demolink', 'str:1:254', $demolink, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('supportlink', 'str:1:254', $supportlink, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('changelog', 'str:1:', $changelog, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('notes', 'str:1:', $notes, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('rstate', 'int:0:6', $rstate, 0, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('usefeedchecked', 'checkbox', $usefeedchecked, false, xarVar::NOT_REQUIRED)) {
               return;
           }
           //if (!xarSec::confirmAuthKey()) return;
            // The user API function is called.
            $usefeed = $usefeedchecked ? 1 : 0;
            $data = xarMod::apiFunc(
                'release',
                'user',
                'getid',
                ['rid' => $rid]
            );

            $exttype = $data['exttype'];
            // The user API function is called.
            if (!xarMod::apiFunc(
                'release',
                'user',
                'createnote',
                ['eid'         => $eid,
                                      'rid'         => $rid,
                                      'version'     => $version,
                                      'price'       => $pricecheck,
                                      'priceterms'  => $price,
                                      'supported'   => $supportcheck,
                                      'demo'        => $democheck,
                                      'dllink'      => $dllink,
                                      'demolink'    => $demolink,
                                      'supportlink' => $supportlink,
                                      'changelog'   => $changelog,
                                      'exttype'     => $exttype,
                                      'notes'       => $notes,
                                      'rstate'      => $rstate,
                                      'usefeed'     => $usefeed, ]
            )) {
                return;
            }

            xarTpl::setPageTitle(xarVar::prepForDisplay(xarML('Thank You')));

           $data = xarTpl::module('release', 'user', 'addnote_thanks');

            break;
    }

    return $data;
}
