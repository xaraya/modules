<?php

function xproject_admin_view($args)
{
    extract($args);
    
    if (!xarVarFetch('verbose', 'checkbox', $verbose, $verbose, XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby', 'str', $sortby, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('q', 'str', $q, '', XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('clientid', 'int', $clientid, $clientid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('memberid', 'int', $memberid, $memberid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('max_priority', 'int', $max_priority, $max_priority, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('max_importance', 'int', $max_importance, $max_importance, XARVAR_NOT_REQUIRED)) return;
    
    $data = array();
    
    $data['verbose'] = $verbose;
    
    $data['projects_objectid'] = xarModGetVar('xproject', 'projects_objectid');
//    xarModAPILoad('xprojects', 'user');
    
    if(!$memberid) {
        $items = xarModAPIFunc('xproject', 'user', 'getall',
                                array('startnum' => $startnum,
                                      'status' => $status,
                                      'sortby' => $sortby,
                                      'clientid' => $clientid,
                                      'max_priority' => $max_priority,
                                      'max_importance' => $max_importance,
                                      'q' => $q,
                                      'numitems' => xarModGetVar('xproject','itemsperpage')));
    } else {
        $items = xarModAPIFunc('xproject', 'user', 'getmemberprojects',
                                array('memberid' => $memberid,
                                      'startnum' => $startnum,
                                      'status' => $status,
                                      'sortby' => $sortby,
                                      'clientid' => $clientid,
                                      'max_priority' => $max_priority,
                                      'max_importance' => $max_importance,
                                      'q' => $q,
                                      'numitems' => xarModGetVar('xproject','itemsperpage')));
    }
    
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
    
    if(!$memberid) {
        $data['pager'] = xarTplGetPager($startnum,
            xarModAPIFunc('xproject', 'user', 'countitems',
                        array('status' => $status,
                              'sortby' => $sortby,
                              'clientid' => $clientid,
                              'max_priority' => $max_priority,
                              'max_importance' => $max_importance,
                              'q' => $q)),
            xarModURL('xproject', 'admin', 'view', array('startnum' => '%%')),
            xarModGetUserVar('xproject', 'itemsperpage', $uid));
    } else {
        $data['pager'] = xarTplGetPager($startnum,
            xarModAPIFunc('xproject', 'user', 'countmemberprojects',
                        array('status' => $status,
                              'sortby' => $sortby,
                              'clientid' => $clientid,
                              'memberid' => $memberid,
                              'max_priority' => $max_priority,
                              'max_importance' => $max_importance,
                              'q' => $q)),
            xarModURL('xproject', 'admin', 'view', array('startnum' => '%%')),
            xarModGetUserVar('xproject', 'itemsperpage', $uid));
    }
        
	return $data;
}

?>