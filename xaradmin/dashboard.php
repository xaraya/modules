<?php

function xtasks_admin_dashboard($args)
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
    
    // get all members with active tasks
    
    $taskowners = xarModAPIFunc('xtasks', 'user', 'getowners', array('mode' => "Open"));
    
    foreach($taskowners as $taskowner) {
        $contactid = $taskowner['contactid'];
        $taskowners[$contactid]['items'] = xarModAPIFunc('xtasks', 'user', 'getall_weighted',
                                    array('owner' => $contactid,
                                        'mode' => "Open",
                                        'numitems' => 3));
        if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    }
    
    
    $data['taskowners'] = $taskowners;
        
    return $data;
}

?>