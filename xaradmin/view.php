<?php

function xtasks_admin_view($args)
{
    extract($args);
    
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mymemberid',   'int', $mymemberid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('memberid',   'int', $memberid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('statusfilter',   'str', $statusfilter,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('orderby',   'str', $orderby,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('q',   'str', $q,   '', XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('xtasks', 'admin', 'menu');

    $data['xtasks_objectid'] = xarModGetVar('xtasks', 'xtasks_objectid');
    
    $data['orderby'] = $orderby;
//    xarModAPILoad('xtaskss', 'user');
    $items = xarModAPIFunc('xtasks', 'user', 'getall',
                            array('mymemberid' => $mymemberid,
                                'memberid' => $memberid,
                                'statusfilter' => $statusfilter,
                                'orderby' => $orderby,
                                'startnum' => $startnum,
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
        xarModAPIFunc('xtasks', 'user', 'countitems', 
                    array('memberid' => $memberid,
                            'statusfilter' => $statusfilter)),
        xarModURL('xtasks', 'admin', 'view', 
                    array('startnum' => '%%',
                            'orderby' => $orderby,
                            'memberid' => $memberid,
                            'statusfilter' => $statusfilter)),
        xarModGetUserVar('xtasks', 'itemsperpage', $uid));
        
    return $data;
}

?>