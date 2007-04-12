<?php

function xtasks_user_mytasks($args)
{        
    extract($args);
    
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('orderby', 'str', $orderby, '', XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('xtasks', 'admin', 'menu');

    if (!xarSecurityCheck('AddXTask')) {
        return;
    }
    
    if(empty($orderby)) $orderby = "priority";
    
    $mymemberid = xarModGetUserVar('xproject', 'mymemberid');

    if (empty($mymemberid)) {
        return;
    }
    
    $data['startnum'] = $startnum;
    $data['orderby'] = $orderby;
    $data['depth'] = 0;
    $data['maxdepth'] = xarModGetVar('xtasks', 'maxdepth');

    $data['authid'] = xarSecGenAuthKey();

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add'));

    $data['xtasks_objectid'] = xarModGetVar('xtasks', 'xtasks_objectid');
//    xarModAPILoad('xtaskss', 'user');
    $items = xarModAPIFunc('xtasks', 'user', 'getall',
                            array('owner' => $mymemberid,
                                  'startnum' => $startnum,
                                  'orderby' => $orderby,
                                  'mode' => "Open",
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
    
    $data['show_importance'] = xarModGetUserVar('xtasks', 'show_importance');
    $data['show_priority'] = xarModGetUserVar('xtasks', 'show_priority');
    $data['show_age'] = xarModGetUserVar('xtasks', 'show_age');
    $data['show_pctcomplete'] = xarModGetUserVar('xtasks', 'show_pctcomplete');
    $data['show_planned_dates'] = xarModGetUserVar('xtasks', 'show_planned_dates');
    $data['show_actual_dates'] = xarModGetUserVar('xtasks', 'show_actual_dates');
    $data['show_hours'] = xarModGetUserVar('xtasks', 'show_hours');
    
    $data['returnurl'] = xarModURL('xtasks', 'user', 'mytasks', 
                    array('startnum' => '%%',
                            'orderby' => $orderby));
    
    $uid = xarUserGetVar('uid');
    
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('xtasks', 'user', 'countitems', 
                    array('memberid' => $mymemberid,
                            'statusfilter' => "Active")),
        xarModURL('xtasks', 'user', 'mytasks', 
                    array('startnum' => '%%',
                            'orderby' => $orderby)),
        xarModGetUserVar('xtasks', 'itemsperpage', $uid));
        
    return $data;
}

?>
