<?php
/*
 * View all Extensions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
function release_admin_viewids()
{
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'enum:modules:themes:all:', $phase, 'all', XARVAR_NOT_REQUIRED)) return;

    // Security Check
    if(!xarSecurityCheck('EditRelease')) return;

    $uid = xarUserGetVar('uid');


   //TODO -hardcode for now until we get the rest working
    if ($phase == 'modules') {
        $exttype=1;
    }elseif ($phase =='themes') {
        $exttype=2;
    }elseif ($phase =='properties') {
        $exttype=3;
    }elseif ($phase =='blocks') {
        $exttype=4;
    }elseif ($phase =='custom') {
        $exttype=5;
    }elseif ($phase =='templatepack') {
        $exttype=6;
    }elseif ($phase =='addon') {
        $exttype=7;
    }else{
     $exttype=1;
    }

    // The user API function is called. 
    $items = xarModAPIFunc('release', 'user', 'getallrids',
                       array('exttype' => $exttype,
                             'startnum' => $startnum,
                             'numitems' => xarModGetUserVar('release',
                                                            'itemsperpage',$uid),
                              ));

    if (empty($items)) {
        $msg = xarML('There are no items to display in the release module');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        $items[$i]['rid'] = xarVarPrepForDisplay($item['rid']);
        $items[$i]['regname'] = xarVarPrepForDisplay($item['regname']);

        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('EditRelease', 0)) {
            $items[$i]['editurl'] = xarModURL('release', 'user', 'modifyid',
                                              array('rid' => $item['rid']));
        } else {
            $items[$i]['editurl'] = '';
        }

        $items[$i]['deletetitle'] = xarML('Delete');
        if (xarSecurityCheck('DeleteRelease', 0)) {
            $items[$i]['deleteurl'] = xarModURL('release', 'admin', 'deleteid',
                                               array('rid' => $item['rid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }

    }
    //Add the pager
    $data['phase']=$phase;
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('release', 'user', 'countitems',array('exttype'=>$exttype)),
        xarModURL('release', 'admin', 'viewids', array('startnum' => '%%','phase'=>$phase)),
        xarModGetUserVar('release', 'itemsperpage', $uid));
    // Add the array of items to the template variables
    $data['items'] = $items;
    return $data;
}

?>