<?php
/**
 * View notes
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
 * Display a release
 *
 * @param rid ID
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_user_viewnotes()
{
    if (!xarVar::fetch('startnum', 'str:1:', $startnum, '1', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('phase', 'str:1:', $phase, 'all', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('filter', 'str:1:', $filter, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('exttype', 'str:1:', $exttype, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    // Security Check
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }

    $uid = xarUser::getVar('id');
    $data['items'] = [];

    if (empty($phase)) {
        $phase = 'viewall';
    }
    $exttypes = xarMod::apiFunc('release', 'user', 'getexttypes');
    $data['exttypes'] = $exttypes;

    switch (strtolower($phase)) {
        case 'viewall':
        default:

            // The user API function is called.
            $items = xarMod::apiFunc(
                'release',
                'user',
                'getallnotes',
                ['startnum' => $startnum,
                                        'numitems' => xarModVars::get(
                                            'release',
                                            'itemsperpage'
                                        ),
                                        'approved' => 2,
                                        'usefeed'  => 1, ]
            );//only those that want to be on the feed
            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('All');
            break;

        case 'certified':

            // The user API function is called.
            $items = xarMod::apiFunc(
                'release',
                'user',
                'getallnotes',
                ['startnum' => $startnum,
                                        'numitems' => xarModVars::get(
                                            'release',
                                            'itemsperpage'
                                        ),
                                        'certified'=> 2, ]
            );

            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('Certified');
            break;

        case 'price':

            // The user API function is called.
            $items = xarMod::apiFunc(
                'release',
                'user',
                'getallnotes',
                ['startnum' => $startnum,
                                        'numitems' => xarModVars::get(
                                            'release',
                                            'itemsperpage'
                                        ),
                                        'price'    => 2, ]
            );

            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('Commercial');
            break;

        case 'free':

            // The user API function is called.
            $items = xarMod::apiFunc(
                'release',
                'user',
                'getallnotes',
                ['startnum' => $startnum,
                                        'numitems' => xarModVars::get(
                                            'release',
                                            'itemsperpage'
                                        ),
                                        'price'    => 1, ]
            );

            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('Free');
            break;

        case 'supported':

            // The user API function is called.
            $items = xarMod::apiFunc(
                'release',
                'user',
                'getallnotes',
                ['startnum' => $startnum,
                                        'numitems' => xarModVars::get(
                                            'release',
                                            'itemsperpage'
                                        ),
                                        'supported'=> 2, ]
            );

            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('Supported');
            break;
    }

    $numitems=count($items);
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < $numitems; $i++) {
        $item = $items[$i];

        // The user API function is called.
        $getid = xarMod::apiFunc(
            'release',
            'user',
            'getid',
            ['eid' => $items[$i]['eid']]
        );


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

        $flipext = array_flip($exttypes);
        $items[$i]['exttype'] = xarVar::prepForDisplay($getid['exttype']);
        $item[$i]['exttypename']=array_search($getid['exttype'], $flipext);
        $items[$i]['class'] = xarVar::prepForDisplay($getid['class']);
        $items[$i]['regname'] = xarVar::prepForDisplay($getid['regname']);
        $items[$i]['rid'] = xarVar::prepForDisplay($getid['rid']);
        $items[$i]['displname'] = xarVar::prepForDisplay($getid['displname']);
        $items[$i]['realname'] = $getuser['name'];
        $items[$i]['desc'] = nl2br(xarVar::prepHTMLDisplay($getid['desc']));
        $items[$i]['notes'] = nl2br(xarVar::prepHTMLDisplay($item['notes']));


        //Add pager
        $data['pager'] = xarTplPager::getPager(
            $startnum,
            xarMod::apiFunc('release', 'user', 'countnotes', ['phase'=>$phase]),
            xarController::URL('release', 'user', 'viewnotes', ['startnum' => '%%','phase'=>$phase,
                                                                           'filter'=>$filter,
                                                                            'exttype' =>$exttype, ]),
            xarModUserVars::get('release', 'itemsperpage', $uid)
        );
    }


    $phase=strtolower($phase);
    $data['phase'] = $phasedesc;
    // Add the array of items to the template variables
    $data['items'] = $items;

    // Return the template variables defined in this function
    return $data;
}
