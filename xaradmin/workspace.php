<?php

function xtasks_admin_workspace($args)
{        
    extract($args);
    
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid', 'int::', $projectid, $projectid, XARVAR_NOT_REQUIRED)) return;
    
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

    $data['authid'] = xarSecGenAuthKey();
    $data['modid'] = $modid;
    $data['itemtype'] = $itemtype;
    $data['objectid'] = $objectid;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add'));

    $data['xtasks_objectid'] = xarModGetVar('xtasks', 'xtasks_objectid');
//    xarModAPILoad('xtaskss', 'user');
    $items = xarModAPIFunc('xtasks', 'user', 'getall',
                            array('projectid' => $projectid,
                                  'modid'    => $modid,
                                  'itemtype' => $itemtype,
                                  'objectid' => $objectid,
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
        
	return $data;
}

?>
