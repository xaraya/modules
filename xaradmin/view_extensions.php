<?php
/*
 * View all extensions
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
 */
function release_admin_view_extensions()
{
    if (!xarSecurity::check('EditRelease')) {
        return;
    }

    // Get the object to be listed
    $data['object'] = DataObjectMaster::getObjectList(['name' => 'release_extensions']);

    return $data;

    /*if (!xarVar::fetch('startnum', 'str:1:', $startnum, '1', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('phase', 'str', $phase, 'all', xarVar::NOT_REQUIRED)) return;

    // Security Check
    if(!xarSecurity::check('EditRelease')) return;

    $uid = xarUser::getVar('id');

    $xarexttypes = xarMod::apiFunc('release','user','getexttypes');
    foreach ($xarexttypes as $k=>$v) {
        $testv = strtolower($v);
        if ($phase == $testv) {
            $exttype=$k;
        }
    }
    // The user API function is called.
    $items = xarMod::apiFunc('release', 'user', 'getallrids',
                       array('exttype'  => $exttype,
                             'startnum' => $startnum,
                             'numitems' => xarModUserVars::get('release',
                                                            'itemsperpage',$uid),
                              ));

    if (empty($items)) return xarTpl::module('release', 'user', 'errors', array('layout' => 'no_items'));

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        $items[$i]['eid'] = xarVar::prepForDisplay($item['eid']);
        $items[$i]['rid'] = xarVar::prepForDisplay($item['rid']);
        $items[$i]['regname'] = xarVar::prepForDisplay($item['regname']);
        $items[$i]['displname'] = xarVar::prepForDisplay($item['displname']);
        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecurity::check('EditRelease', 0)) {
            $items[$i]['editurl'] = xarController::URL('release', 'user', 'modifyid',
                                              array('eid' => $item['eid']));
        } else {
            $items[$i]['editurl'] = '';
        }

        $items[$i]['deletetitle'] = xarML('Delete');
        if (xarSecurity::check('ManageRelease', 0)) {
            $items[$i]['deleteurl'] = xarController::URL('release', 'admin', 'deleteid',
                                               array('eid' => $item['eid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }

    }
    //Add the pager
    $data['phase']=$phase;
    $data['pager'] = xarTplPager::getPager($startnum,
        xarMod::apiFunc('release', 'user', 'countitems',array('exttype'=>$exttype)),
        xarController::URL('release', 'admin', 'viewids', array('startnum' => '%%','phase'=>$phase)),
        xarModUserVars::get('release', 'itemsperpage', $uid));
    // Add the array of items to the template variables
    $data['items'] = $items;
    return $data;
    */
}
