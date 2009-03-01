<?php

function xtasks_admin_assignments($args)
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
//    xarModAPILoad('xtasks', 'user');
    $items = xarModAPIFunc('xtasks', 'user', 'getassignments');
    
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    for ($i = 0; $i < count($items); $i++) {
        if(isset($item['taskid'])) {
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
    }
    
    $data['authid'] = xarSecGenAuthKey();
    $data['authid2'] = xarSecGenAuthKey('xproject');
    $data['returnurl'] = xarModURL('xtasks','admin','assignments');
    $data['items'] = $items;
        
    return $data;
}

?>