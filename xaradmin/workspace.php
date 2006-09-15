<?php

function xtasks_admin_workspace($args)
{        
    extract($args);
    
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('orderby', 'str', $orderby, '', XARVAR_NOT_REQUIRED)) return;
    
    $data = array();

    if (!xarSecurityCheck('AddXTask')) {
        return;
    }
    
    if (isset($extrainfo) && is_array($extrainfo)) {
        if (isset($extrainfo['module']) && is_string($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        }
        if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
            $itemtype = $extrainfo['itemtype'];
        }
        if (isset($extrainfo['returnurl']) && is_string($extrainfo['returnurl'])) {
            $data['returnurl'] = $extrainfo['returnurl'];
        }
    } else {
        $data['returnurl'] = $extrainfo;
    }
    
    if(!strpos($data['returnurl'], "mode=tasks")) {
        $data['returnurl'] = $data['returnurl']."&amp;mode=tasks";
    }
    
    if (isset($args['modid'])) {
        $modid = $args['modid'];
    } elseif (!empty($args['extrainfo']) && !empty($args['extrainfo']['module'])) {
        if (is_numeric($args['extrainfo']['module'])) {
            $modid = $args['extrainfo']['module'];
        } else {
            $modid = xarModGetIDFromName($args['extrainfo']['module']);
        }
    } else {
        xarVarFetch('modid','isset',$modid,NULL,XARVAR_NOT_REQUIRED);
        if (empty($modid)) {
            $modid = xarModGetIDFromName(xarModGetName());
        }
    }

    if (isset($args['itemtype'])) {
        $itemtype = $args['itemtype'];
    } elseif (!empty($args['extrainfo']) && isset($args['extrainfo']['itemtype'])) {
        $itemtype = $args['extrainfo']['itemtype'];
    } else {
        xarVarFetch('itemtype','isset',$itemtype,NULL,XARVAR_NOT_REQUIRED);
    }

    if (isset($args['objectid'])) {
        $objectid = $args['objectid'];
    } else {
        xarVarFetch('objectid','isset',$objectid,NULL,XARVAR_NOT_REQUIRED);
    }
    
    $data['startnum'] = $startnum;
    $data['orderby'] = $orderby ? $orderby : "status";
    $data['depth'] = 0;
    $data['maxdepth'] = xarModGetVar('xtasks', 'maxdepth');

    $data['authid'] = xarSecGenAuthKey('xtasks');
    $data['modid'] = $modid;
    $data['itemtype'] = $itemtype;
    $data['objectid'] = $objectid;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add'));

    $data['xtasks_objectid'] = xarModGetVar('xtasks', 'xtasks_objectid');
//    xarModAPILoad('xtaskss', 'user');
    $items = xarModAPIFunc('xtasks', 'user', 'getall',
                            array('modid'    => $modid,
                                  'itemtype' => $itemtype,
                                  'objectid' => $objectid,
                                  'startnum' => $startnum,
                                  'memberid' => isset($memberid) ? $memberid : false,
                                  'mymemberid' => isset($mymemberid) ? $mymemberid : false,
                                  'orderby'  => $orderby,
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
