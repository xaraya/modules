<?php
/*
 * View all extensions
 *
 * @package modules
 * @subpackage Release Module
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
 */
function release_admin_view_extensions()
{
    if (!xarSecurityCheck('EditRelease')) return;

    // Get the object to be listed
    $data['object'] = DataObjectMaster::getObjectList(array('name' => 'release_extensions'));

    return $data;

    /*if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'str', $phase, 'all', XARVAR_NOT_REQUIRED)) return;

    // Security Check
    if(!xarSecurityCheck('EditRelease')) return;

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
        $items[$i]['eid'] = xarVarPrepForDisplay($item['eid']);
        $items[$i]['rid'] = xarVarPrepForDisplay($item['rid']);
        $items[$i]['regname'] = xarVarPrepForDisplay($item['regname']);
        $items[$i]['displname'] = xarVarPrepForDisplay($item['displname']);
        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('EditRelease', 0)) {
            $items[$i]['editurl'] = xarModURL('release', 'user', 'modifyid',
                                              array('eid' => $item['eid']));
        } else {
            $items[$i]['editurl'] = '';
        }

        $items[$i]['deletetitle'] = xarML('Delete');
        if (xarSecurityCheck('ManageRelease', 0)) {
            $items[$i]['deleteurl'] = xarModURL('release', 'admin', 'deleteid',
                                               array('eid' => $item['eid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }

    }
    //Add the pager
    $data['phase']=$phase;
    $data['pager'] = xarTplGetPager($startnum,
        xarMod::apiFunc('release', 'user', 'countitems',array('exttype'=>$exttype)),
        xarModURL('release', 'admin', 'viewids', array('startnum' => '%%','phase'=>$phase)),
        xarModUserVars::get('release', 'itemsperpage', $uid));
    // Add the array of items to the template variables
    $data['items'] = $items;
    return $data;
    */
}

?>