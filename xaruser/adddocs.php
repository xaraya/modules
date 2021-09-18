<?php
/**
 * Display a release
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Display a release
 *
 * @param rid ID
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_user_adddocs()
{
    // Security Check
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }
    if (!xarVar::fetch('rid', 'isset', $rid, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('phase', 'str:1:', $phase, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('exttype', 'isset', $exttype, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('eid', 'isset', $eid, null, xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['items'] = [];
    $data['rid'] = $rid;
    $data['eid'] = $eid;
    if (empty($phase)) {
        $phase = 'getmodule';
    }

    switch (strtolower($phase)) {
        case 'getmodule':
        default:
            // First we need to get the module that we are adding the release note to.
            // This will be done in several stages so that the information is accurate.

            $authid = xarSec::genAuthKey();
            $data = xarTpl::module('release', 'user', 'adddocs_getmodule', ['authid'    => $authid]);

            xarTpl::setPageTitle(xarVar::prepForDisplay(xarML('Documentation')));

            break;

        case 'start':
            // First we need to get the module that we are adding the release note to.
            // This will be done in several stages so that the information is accurate.

           if (!xarVar::fetch('rid', 'isset', $rid, null, xarVar::NOT_REQUIRED)) {
               return;
           }

            // The user API function is called.
            $data = xarMod::apiFunc(
                'release',
                'user',
                'getid',
                ['eid' => $eid]
            );


            $uid = xarUser::getVar('id');

            if (($data['uid'] == $uid) or (xarSecurity::check('EditRelease', 0))) {
                $message = '';
            } else {
                $message = xarML('You are not allowed to add documentation to this module');
            }

            //TODO FIX ME!!!
            if (empty($data['name'])) {
                $message = xarML('There is no assigned ID for your extension.');
                $data['name']='';
            }

            xarTpl::setPageTitle(xarVar::prepForDisplay($data['name']));

            $authid = xarSec::genAuthKey();
            $data = xarTpl::module(
                'release',
                'user',
                'adddocs_start',
                ['rid'       => $data['rid'],
                        'eid'       => $data['eid'],
                        'name'      => $data['name'],
                        'desc'      => $data['desc'],
                        'exttype'      => $data['exttype'],
                        'message'   => $message,
                        'authid'    => $authid, ]
            );

            break;

        case 'module':

            $data['mtype'] = 'mgeneral';
            $data['return'] = 'module';
            // The user API function is called.

            $items = xarMod::apiFunc(
                'release',
                'user',
                'getdocs',
                ['eid' => $eid,
                                          'exttype'=> $data['mtype'], ]
            );

            if (empty($items)) {
                $data['message'] = xarML('There is no general module documentation defined');
            }


            xarTpl::setPageTitle(xarVar::prepForDisplay(xarML('General Information')));


            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUser::getVar('id');
                $items[$i]['docsf'] = nl2br(xarVar::prepHTMLDisplay($item['docs']));
            }


            $data['items'] = $items;
            $data['authid'] = xarSec::genAuthKey();

            break;

        case 'theme':

            $data['mtype'] = 'tgeneral';
            $data['return'] = 'theme';
            // The user API function is called.

            $items = xarMod::apiFunc(
                'release',
                'user',
                'getdocs',
                ['eid' => $eid,
                                          'exttype'=> $data['mtype'], ]
            );

            if (empty($items)) {
                $data['message'] = xarML('There is no general theme documentation defined');
            }


            xarTpl::setPageTitle(xarVar::prepForDisplay(xarML('General Information')));


            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUser::getVar('id');
                $items[$i]['docsf'] = nl2br(xarVar::prepHTMLDisplay($item['docs']));
            }


            $data['items'] = $items;
            $data['authid'] = xarSec::genAuthKey();

            break;

        case 'blockgroups':

            $data['mtype'] = 'bgroups';
            $data['return'] = 'blockgroups';
            // The user API function is called.

            $items = xarMod::apiFunc(
                'release',
                'user',
                'getdocs',
                ['eid' => $eid,
                                          'type'=> $data['mtype'], ]
            );

            if (empty($items)) {
                $data['message'] = xarML('There is no block groups documentation defined');
            }


            xarTpl::setPageTitle(xarVar::prepForDisplay(xarML('General Information')));


            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUser::getVar('id');
                $items[$i]['docsf'] = nl2br(xarVar::prepHTMLDisplay($item['docs']));
            }


            $data['items'] = $items;
            $data['authid'] = xarSec::genAuthKey();

            break;

        case 'blocks':

            $data['mtype'] = 'mblocks';
            $data['return'] = 'blocks';
            // The user API function is called.

            $items = xarMod::apiFunc(
                'release',
                'user',
                'getdocs',
                ['eid' => $eid,
                                          'exttype'=> $data['mtype'], ]
            );

            if (empty($items)) {
                $data['message'] = xarML('There is no blocks documentation defined');
            }


            xarTpl::setPageTitle(xarVar::prepForDisplay(xarML('General Information')));


            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUser::getVar('id');
                $items[$i]['docsf'] = nl2br(xarVar::prepHTMLDisplay($item['docs']));
            }


            $data['items'] = $items;
            $data['authid'] = xarSec::genAuthKey();


            break;

        case 'hooks':

            $data['mtype'] = 'mhooks';
            $data['return'] = 'hooks';
            // The user API function is called.

            $items = xarMod::apiFunc(
                'release',
                'user',
                'getdocs',
                ['eid' => $eid,
                                          'exttype'=> $data['mtype'], ]
            );

            if (empty($items)) {
                $data['message'] = xarML('There is no hook documentation defined');
            }


            xarTpl::setPageTitle(xarVar::prepForDisplay(xarML('General Information')));


            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUser::getVar('id');
                $items[$i]['docsf'] = nl2br(xarVar::prepHTMLDisplay($item['docs']));
            }


            $data['items'] = $items;
            $data['authid'] = xarSec::genAuthKey();

            break;

        case 'update':
            if (!xarVar::fetch('rid', 'isset', $rid, null, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('mtype', 'isset', $mtype, null, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('title', 'str:1:', $title, null, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('return', 'isset', $return, null, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('doc', 'isset', $doc, null, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('eid', 'isset', $eid, null, xarVar::NOT_REQUIRED)) {
                return;
            }

           if (!xarSec::confirmAuthKey()) {
               return;
           }

           if (!xarSecurity::check('EditRelease', 0)) {
               $approved = 1;
           } else {
               $approved = 2;
           }

            // The user API function is called.
            if (!xarMod::apiFunc(
                'release',
                'user',
                'createdoc',
                ['eid'         => $eid,
                                      'rid'         => $rid,
                                      'type'        => $mtype,
                                      'title'       => $title,
                                      'doc'         => $doc,
                                      'approved'    => $approved, ]
            )) {
                return;
            }

            xarController::redirect(xarController::URL('release', 'user', 'adddocs', ['phase' => $return,
                                                                              'eid' => $eid, ]));

           $data = '';
            break;
    }

    return $data;
}
