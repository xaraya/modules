<?php

function release_admin_viewids()
{
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'str:1:', $phase, 'all', XARVAR_NOT_REQUIRED)) return;

    // Security Check
    if(!xarSecurityCheck('EditRelease')) return;
    $phase = xarVarCleanFromInput('phase');

    $uid = xarUserGetVar('uid');

    if (!isset($idtypes)) {
       $idtypes=1;
    }

    if ($phase == 'modules') {
        $idtypes=3;
    } elseif ($phase =='themes') {
        $idtypes=2;
    } else{
        $idtypes=1;
    }

    // The user API function is called. 
    $items = xarModAPIFunc('release',
                           'user',
                           'getallids',
                       array('idtypes' => $idtypes,
                             'startnum' => $startnum,
                             'numitems' => xarModGetUserVar('release',
                                                            'itemsperpage',$uid),
                              ));

    if (empty($items)) {
        $msg = xarML('There are no items to display in the release module');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        $items[$i]['rid'] = xarVarPrepForDisplay($item['rid']);
        $items[$i]['regname'] = xarVarPrepForDisplay($item['regname']);

        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('EditRelease', 0)) {
            $items[$i]['editurl'] = xarModURL('release',
                                              'user',
                                              'modifyid',
                                              array('rid' => $item['rid']));
        } else {
            $items[$i]['editurl'] = '';
        }

        $items[$i]['deletetitle'] = xarML('Delete');
        if (xarSecurityCheck('DeleteRelease', 0)) {
            $items[$i]['deleteurl'] = xarModURL('release',
                                               'admin',
                                               'deleteid',
                                               array('rid' => $item['rid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }

    }
    //Add the pager
    $data['phase']=$phase;
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('release', 'user', 'countitems',array('idtypes'=>$idtypes)),
        xarModURL('release', 'admin', 'viewids', array('startnum' => '%%','phase'=>$phase)),
        xarModGetUserVar('release', 'itemsperpage', $uid));

    // Add the array of items to the template variables
    $data['items'] = $items;
    return $data;
}

?>