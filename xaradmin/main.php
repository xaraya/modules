<?php

function xproject_admin_main($args)
{
    extract($args);
    
    if (!xarVarFetch('verbose', 'checkbox', $verbose, $verbose, XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby', 'str', $sortby, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('q', 'str', $q, '', XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('clientid', 'int', $clientid, $clientid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('memberid', 'int', $memberid, $memberid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mymemberid', 'int', $mymemberid, $mymemberid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('max_priority', 'int', $max_priority, $max_priority, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('max_importance', 'int', $max_importance, $max_importance, XARVAR_NOT_REQUIRED)) return;
    
    $args['verbose'] = $verbose;
    $args['status'] = $status;
    $args['sortby'] = $sortby;
    $args['q'] = $q;
    $args['clientid'] = $clientid;
    $args['memberid'] = $mymemberid ? $mymemberid : $memberid;
    $args['max_priority'] = $max_priority;
    $args['max_importance'] = $max_importance;
    
    $data = xarModAPIFunc('xproject', 'admin', 'menu', array('showsearch' => true));
    
    $data['showsearch'] = 1;
    
    if(!$args['memberid']) {
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
                                array('memberid' => $args['memberid'],
                                      'startnum' => $startnum,
                                      'status' => $status,
                                      'sortby' => $sortby,
                                      'clientid' => $clientid,
                                      'max_priority' => $max_priority,
                                      'max_importance' => $max_importance,
                                      'q' => $q,
                                      'numitems' => xarModGetVar('xproject','itemsperpage')));
    }
    $args['items'] = $items;
    
    $args['returnurl'] = xarModURL('xproject', 
                                'admin', 
                                'view', 
                                array('startnum' => '%%',
                                      'status' => $status,
                                      'sortby' => $sortby,
                                      'clientid' => $clientid,
                                      'max_priority' => $max_priority,
                                      'max_importance' => $max_importance,
                                      'q' => $q));
	
    $args['authid'] = xarSecGenAuthKey();
    $args['inline'] = 0;
    
    $data['projectlist'] = xarTplModule('xproject', 
                                        'admin', 
                                        'view',
                                        $args);

	return $data;
}

?>