<?php

function xtasks_admin_search($args)
{        
    extract($args);
    
    if(!xarModLoad('addressbook', 'user')) return;
    
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('orderby', 'str', $orderby, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('verbose', 'checkbox', $verbose, $verbose, XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('status', 'str', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('q', 'str', $q, '', XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('memberid', 'int', $memberid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mymemberid', 'int', $mymemberid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('max_priority', 'int', $max_priority, 9, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('max_importance', 'int', $max_importance, 9, XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('xtasks','admin','menu');

    if (!xarSecurityCheck('AddXTask')) {
        return;
    }
    
    $modid = xarModGetIDFromName(xarModGetName());
    $itemtype = 1;
    
    $returnurl = xarModURL('xtasks', 'admin', 'search',
                        array('orderby' => $orderby,
                            'q' => $q,
                            'status' => $status,
                            'memberid' => $memberid,
                            'max_priority' => $max_priority,
                            'max_importance' => $max_importance));
    
    $data['returnurl'] = $returnurl."&amp;mode=tasklist";
    
    $data['q'] = $q;
    $data['depth'] = 0;
    $data['verbose'] = $verbose;
    $data['memberid'] = $memberid;
    $data['mymemberid'] = $mymemberid;
    $data['max_priority'] = $max_priority;
    $data['max_importance'] = $max_importance;
    $data['startnum'] = $startnum;
    $data['orderby'] = $orderby;
    $data['maxdepth'] = xarModGetVar('xtasks', 'maxdepth');

    $data['authid'] = xarSecGenAuthKey('xtasks');
    $data['modid'] = $modid;
    $data['itemtype'] = $itemtype;
    
//    xarModAPILoad('xtaskss', 'user');
    $items = xarModAPIFunc('xtasks', 'user', 'getall',
                            array('parentid'    => 0,
                                  'startnum' => $startnum,
                                  'q' => $q,
                                  'status' => $status,
                                  'memberid' => isset($memberid) ? $memberid : false,
                                  'mymemberid' => isset($mymemberid) ? $mymemberid : false,
                                  'max_priority' => $max_priority,
                                  'max_importance' => $max_importance,
                                  'orderby' => $orderby,
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
    
    $teammembers = xarModAPIFunc('xproject', 'team', 'getmembers');
    
    $data['teammembers'] = $teammembers;
    
    $data['show_importance'] = xarModGetUserVar('xtasks', 'show_importance');
    $data['show_priority'] = xarModGetUserVar('xtasks', 'show_priority');
    $data['show_age'] = xarModGetUserVar('xtasks', 'show_age');
    $data['show_pctcomplete'] = xarModGetUserVar('xtasks', 'show_pctcomplete');
    $data['show_planned_dates'] = xarModGetUserVar('xtasks', 'show_planned_dates');
    $data['show_actual_dates'] = xarModGetUserVar('xtasks', 'show_actual_dates');
    $data['show_hours'] = xarModGetUserVar('xtasks', 'show_hours');
    $data['show_owner'] = xarModGetUserVar('xtasks', 'show_owner');
    $data['show_project'] = xarModGetUserVar('xtasks', 'show_project');
    $data['show_client'] = xarModGetUserVar('xtasks', 'show_client');
        
    return $data;
}

?>
