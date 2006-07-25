<?php

function xtasks_admin_view()
{
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    $data = xarModAPIFunc('xtasks', 'admin', 'menu');

    $data['xtasks_objectid'] = xarModGetVar('xtasks', 'xtasks_objectid');
//    xarModAPILoad('xtaskss', 'user');
    $items = xarModAPIFunc('xtasks', 'user', 'getall',
                            array('startnum' => $startnum,
                                  'numitems' => xarModGetVar('xtasks','itemsperpage')));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        $items[$i]['link'] = xarModURL('xtasks',
            'admin',
            'display',
            array('taskid' => $item['taskid']));
        if (xarSecurityCheck('EditXTask', 0, 'Item', "$item[task_name]:All:$item[taskid]")) {
            $items[$i]['editurl'] = xarModURL('xtasks',
                'admin',
                'modify',
                array('taskid' => $item['taskid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        if (xarSecurityCheck('DeleteXTask', 0, 'Item', "$item[task_name]:All:$item[taskid]")) {
            $items[$i]['deleteurl'] = xarModURL('xtasks',
                'admin',
                'delete',
                array('taskid' => $item['taskid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
    }
    
    $data['items'] = $items;
    
    $uid = xarUserGetVar('uid');
    
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('xtasks', 'user', 'countitems'),
        xarModURL('xtasks', 'admin', 'view', array('startnum' => '%%')),
        xarModGetUserVar('xtasks', 'itemsperpage', $uid));
        
	return $data;
}

?>