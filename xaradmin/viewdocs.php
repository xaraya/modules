<?php
/**
 * View the documentation
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 */
/**
 * View the Documentation
 *
 * @param
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_admin_viewdocs()
{
    // Security Check
    if (!xarSecurity::check('EditRelease')) {
        return;
    }

    // Get parameters
    if (!xarVar::fetch('phase', 'enum:unapproved:viewall:certified:price:supported', $phase, 'unapproved', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('startnum', 'int', $starnum, 1, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('filter', 'str', $filter, $filter, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('exttype', 'str', $exttype, $exttype, xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['items'] = [];

    if (empty($phase)) {
        $phase = 'unapproved';
    }

    switch (strtolower($phase)) {
        case 'unapproved':

            $items = xarMod::apiFunc(
                'release',
                'user',
                'getdocs',
                ['approved' => 1]
            );

            if ($items == false) {
                $data['message'] = xarML('There are no pending release notes');
            }

            break;

        case 'viewall':
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
                                        'approved' => 2, ]
            );
            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }

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

            break;
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
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
    }


    // Add the array of items to the template variables
    $data['items'] = $items;

    // TODO : add a pager (once it exists in BL)
    $data['pager'] = '';

    // Return the template variables defined in this function
    return $data;
}
