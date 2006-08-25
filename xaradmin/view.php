<?php

function xproject_admin_view($args)
{
    extract($args);
    
    if (!xarVarFetch('verbose', 'checkbox', $verbose, 0, XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str', $status, $status, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby', 'str', $sortby, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('q', 'str', $q, '', XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('inline', 'int', $inline, $inline, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('clientid', 'int', $clientid, $clientid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('memberid', 'int', $memberid, $memberid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('max_priority', 'int', $max_priority, $max_priority, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('max_importance', 'int', $max_importance, $max_importance, XARVAR_NOT_REQUIRED)) return;
    
    $data = array();
    
    if(isset($verbose)) $data['verbose'] = $verbose;
    
//    $data['projects_objectid'] = xarModGetVar('xproject', 'projects_objectid');
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
            xarModURL('xproject', 'admin', 'view', array('startnum' => '%%'))
            ."\" onClick=\"return loadContent(this.href,'projectlist')\"",
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
            xarModURL('xproject', 
                    'admin', 
                    'view', 
                    array('startnum' => '%%',
                          'status' => $status,
                          'sortby' => $sortby,
                          'clientid' => $clientid,
                          'max_priority' => $max_priority,
                          'max_importance' => $max_importance,
                          'q' => $q))
            ."\" onClick=\"return loadContent(this.href,'projectlist')\"",
            xarModGetUserVar('xproject', 'itemsperpage', $uid));
    }
    
    $data['returnurl'] = xarModURL('xproject', 
                                'admin', 
                                'view', 
                                array('startnum' => '%%',
                                      'status' => $status,
                                      'sortby' => $sortby,
                                      'clientid' => $clientid,
                                      'max_priority' => $max_priority,
                                      'max_importance' => $max_importance,
                                      'q' => $q,
                                      'inline' => 1));
	
    $data['authid'] = xarSecGenAuthKey();
    $data['inline'] = $inline;
    
	return $data;
}

?>