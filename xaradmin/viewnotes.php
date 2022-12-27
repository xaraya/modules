<?php
/**
 * View notes
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
 * view notes
 *
 * @param $startnum
 * @param $phase
 * @param $filter
 * @param $type
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_admin_viewnotes()
{
    if (!xarVar::fetch('startnum', 'int:1:', $startnum, 1, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('phase', 'str:1:', $phase, 'all', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('filter', 'str:1:', $filter, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('exttype', 'str:1:', $exttype, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    // Security Check
    if (!xarSecurity::check('EditRelease')) {
        return;
    }

    $uid = xarUser::getVar('id');
    $data['items'] = [];

    if (empty($phase)) {
        $phase = 'unapproved';
    }
    switch (strtolower($phase)) {
        case 'unapproved':
        default:

            // The user API function is called.
            $items = xarMod::apiFunc(
                'release',
                'user',
                'getallnotes',
                ['startnum' => $startnum,
                                        'numitems' => xarModVars::get(
                                            'roles',
                                            'itemsperpage'
                                        ),
                                        'unapproved' => 1, ]
            );
            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('Pending');
            break;

        case 'viewall':

            // The user API function is called.
            $items = xarMod::apiFunc(
                'release',
                'user',
                'getallnotes',
                ['startnum' => $startnum,
                                        'numitems' => xarModVars::get(
                                            'roles',
                                            'itemsperpage'
                                        ),
                                        'approved' => 2, ]
            );
            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('All Approved');
            break;

        case 'certified':

            // The user API function is called.
            $items = xarMod::apiFunc(
                'release',
                'user',
                'getallnotes',
                ['startnum' => $startnum,
                                        'numitems' => xarModVars::get(
                                            'roles',
                                            'itemsperpage'
                                        ),
                                        'certified'=> $filter, ]
            );

            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }
            if ($filter == 1) {
                $phasedesc =xarML('Non-Certified');
            } else {
                $phasedesc =xarML('Certified');
            }
            break;

        case 'price':

            // The user API function is called.
            $items = xarMod::apiFunc(
                'release',
                'user',
                'getallnotes',
                ['startnum' => $startnum,
                                        'numitems' => xarModVars::get(
                                            'roles',
                                            'itemsperpage'
                                        ),
                                        'price'    => $filter, ]
            );

            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }
            if ($filter == 1) {
                $phasedesc =xarML('Free');
            } else {
                $phasedesc =xarML('Commercial');
            }

            break;

        case 'supported':

            // The user API function is called.
            $items = xarMod::apiFunc(
                'release',
                'user',
                'getallnotes',
                ['startnum' => $startnum,
                                        'numitems' => xarModVars::get(
                                            'roles',
                                            'itemsperpage'
                                        ),
                                        'supported'=> $filter, ]
            );

            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }
            if ($filter == 1) {
                $phasedesc =xarML('Not Supported');
            } else {
                $phasedesc =xarML('Supported');
            }

            break;
    }
    $numitems=count($items);
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < $numitems; $i++) {
        $item = $items[$i];

        if (xarSecurity::check('EditRelease', 0)) {
            $items[$i]['editurl'] = xarController::URL(
                'release',
                'admin',
                'modifynote',
                ['rnid' => $item['rnid']]
            );
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecurity::check('ManageRelease', 0)) {
            $items[$i]['deleteurl'] = xarController::URL(
                'release',
                'admin',
                'deletenote',
                ['rnid' => $item['rnid']]
            );
        } else {
            $items[$i]['deleteurl'] = '';
        }
        $items[$i]['deletetitle'] = xarML('Delete');


        // The user API function is called.
        $getid = xarMod::apiFunc(
            'release',
            'user',
            'getid',
            ['rid' => $items[$i]['rid']]
        );

        $items[$i]['exttype'] = xarVar::prepForDisplay($getid['exttype']);
        $items[$i]['regname'] = xarVar::prepForDisplay($getid['regname']);
        $items[$i]['displaylink'] =  xarController::URL(
            'release',
            'user',
            'displaynote',
            ['rnid' => $item['rnid']]
        );

        $getuser = xarMod::apiFunc(
            'roles',
            'user',
            'get',
            ['uid' => $getid['uid']]
        );

        $items[$i]['contacturl'] = xarController::URL(
            'roles',
            'user',
            'display',
            ['uid' => $getid['uid']]
        );


        $items[$i]['realname'] = $getuser['name'];
        $items[$i]['desc'] = xarVar::prepForDisplay($getid['desc']);

        if ($item['certified'] == 1) {
            $items[$i]['certifiedstatus'] = xarML('Yes');
        } else {
            $items[$i]['certifiedstatus'] = xarML('No');
        }
        $items[$i]['changelog'] = nl2br($item['changelog']);
        $items[$i]['notes'] = nl2br($item['notes']);

        $data['pager'] = xarTplPager::getPager(
            $startnum,
            xarMod::apiFunc('release', 'user', 'countnotes', ['phase'=>$phase,'filter'=>$filter]),
            xarController::URL('release', 'admin', 'viewnotes', ['startnum' => '%%','phase'=>$phase, 'filter'=>$filter]),
            xarModUserVars::get('release', 'itemsperpage', $uid)
        );
    }

    $data['phase'] = $phasedesc;
    // Add the array of items to the template variables
    $data['items'] = $items;
    $data['numitems']=$numitems;
    $data['phasedesc']=$phasedesc;

    // Return the template variables defined in this function
    return $data;
}
