<?php

function xproject_admin_view()
{
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    $data = xarModAPIFunc('xproject', 'admin', 'menu');
//    xarModAPILoad('xprojects', 'user');
    $items = xarModAPIFunc('xproject', 'user', 'getall',
                            array('startnum' => $startnum,
                                  'numitems' => xarModGetVar('xproject','itemsperpage')));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        $items[$i]['link'] = xarModURL('xproject',
            'admin',
            'display',
            array('projectid' => $item['projectid']));
        if (xarSecurityCheck('EditXProject', 0, 'Item', "$item[project_name]:All:$item[projectid]")) {
            $items[$i]['editurl'] = xarModURL('xproject',
                'admin',
                'modify',
                array('projectid' => $item['projectid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        if (xarSecurityCheck('DeleteXProject', 0, 'Item', "$item[project_name]:All:$item[projectid]")) {
            $items[$i]['deleteurl'] = xarModURL('xproject',
                'admin',
                'delete',
                array('projectid' => $item['projectid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
    }
    
    $data['items'] = $items;
    
    $uid = xarUserGetVar('uid');
    
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('xproject', 'user', 'countitems'),
        xarModURL('xproject', 'admin', 'view', array('startnum' => '%%')),
        xarModGetUserVar('xproject', 'itemsperpage', $uid));
        
	return $data;
}

?>