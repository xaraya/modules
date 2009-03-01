<?php

function xtasks_admin_calendar($args)
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
//    xarModAPILoad('xtasks', 'user');
    // determine friday of current week
    $dayofweek = date('w');
    $friday = time() + ((5 - $dayofweek) * (24 * 3600));
    $items = xarModAPIFunc('xtasks', 'user', 'getall',
                            array('mode' => "Calendar",
                                'date_end_planned' => date("Y-m-d", $friday),
                                'show_project' => true,
                                'numitems' => xarModGetVar('xtasks','itemsperpage')));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    $calendardates = array();
    // $calendardates[month][day][owner] = $task
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        if(!empty($item['date_end_planned'])) {
            $time_end_planned = strtotime($item['date_end_planned']);
            $month = date("n", $time_end_planned);
            $day = date("j", $time_end_planned);

            $owner = $item['owner'];

            $calendardates[$month][$day][$owner][$item['taskid']] = $item;
            
        }
    }
    
    foreach($items as $month => $daytasks) {
        foreach($daytasks as $day => $ownertasks) {
            if(isset($calendardates[$month][$day])) {
                sort($calendardates[$month][$day]);
            }
        }
        if(isset($calendardates[$month])) {
            sort($calendardates[$month]);
        }
    }
                        
                        
    
    $data['items'] = $calendardates;
        
    return $data;
}

?>