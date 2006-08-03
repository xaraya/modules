<?php

function xproject_admin_view($args)
{
    extract($args);

    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby', 'str', $sortby, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('q', 'str', $q, '', XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('clientid', 'int', $clientid, $clientid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('memberid', 'int', $memberid, $memberid, XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('xproject', 'admin', 'menu', array('showsearch' => true));
    
    $data['showsearch'] = 1;
    
    $data['projects_objectid'] = xarModGetVar('xproject', 'projects_objectid');
//    xarModAPILoad('xprojects', 'user');
    
    if(!$memberid) {
        $items = xarModAPIFunc('xproject', 'user', 'getall',
                                array('startnum' => $startnum,
                                      'status' => $status,
                                      'sortby' => $sortby,
                                      'clientid' => $clientid,
                                      'max_priority' => $data['max_priority'],
                                      'max_importance' => $data['max_importance'],
                                      'q' => $q,
                                      'numitems' => xarModGetVar('xproject','itemsperpage')));
    } else {
        $items = xarModAPIFunc('xproject', 'user', 'getmemberprojects',
                                array('memberid' => $memberid,
                                      'startnum' => $startnum,
                                      'status' => $status,
                                      'sortby' => $sortby,
                                      'clientid' => $clientid,
                                      'max_priority' => $data['max_priority'],
                                      'max_importance' => $data['max_importance'],
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
    
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('xproject', 'user', 'countitems',
                    array('status' => $status,
                          'sortby' => $sortby,
                          'clientid' => $clientid,
                          'max_priority' => $data['max_priority'],
                          'max_importance' => $data['max_importance'],
                          'q' => $q)),
        xarModURL('xproject', 'admin', 'view', array('startnum' => '%%')),
        xarModGetUserVar('xproject', 'itemsperpage', $uid));
        
	return $data;
}

?>