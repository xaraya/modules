<?php

function xtasks_user_taskarchive($args)
{
    extract($args);
    
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mymemberid',   'int', $mymemberid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('memberid',   'int', $memberid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('statusfilter',   'str', $statusfilter,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('orderby',   'str', $orderby,   '', XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('xtasks', 'admin', 'menu');

    $data['xtasks_objectid'] = xarModGetVar('xtasks', 'xtasks_objectid');
    
    $data['orderby'] = $orderby;
//    xarModAPILoad('xtaskss', 'user');
    $items = xarModAPIFunc('xtasks', 'user', 'getall',
                            array('statusfilter' => "Closed",
                                'orderby' => $orderby,
                                'startnum' => $startnum,
                                'numitems' => xarModGetVar('xtasks','itemsperpage')));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    $tasklist = array();
    foreach($items as $item) {
        $taskinfo = $item;
        $taskinfo['project_name'] = "";
        $taskinfo['projectinfo'] = array();
        $taskinfo['project_url'] = "";
        if($item['projectid'] > 0 && $item['modid'] = xarModGetIDFromName('xproject')) {
            $projectinfo = xarModAPIFunc('xproject',
                                  'user',
                                  'get',
                                  array('projectid' => $item['projectid'])); 
                                  
            if (xarCurrentErrorType() == XAR_SYSTEM_EXCEPTION && xarCurrentErrorID() == 'ID_NOT_EXIST') {
                xarErrorHandled();
            }
        
            if ($projectinfo) {
                $taskinfo['project_name'] = $projectinfo['project_name'];
                $taskinfo['projectinfo'] = $projectinfo;
                $taskinfo['project_url'] = xarModURL('xproject', 'admin', 'display', array('projectid' => $projectinfo['projectid']));
            }
        }
        $tasklist[] = $taskinfo;
    }
    
    $data['items'] = $tasklist;
    
    $data['returnurl'] = xarModURL('xtasks', 'user', 'taskarchive', 
                    array('startnum' => '%%',
                            'orderby' => $orderby));
    
    $uid = xarUserGetVar('uid');
    
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('xtasks', 'user', 'countitems', 
                    array('statusfilter' => "Closed")),
        $data['returnurl'],
        xarModGetUserVar('xtasks', 'itemsperpage', $uid));
        
    return $data;
}

?>