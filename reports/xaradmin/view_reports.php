<?php
/*
 * Display a list of defined reports
 */
function reports_admin_view_reports() 
{
	// Get a list of reports
	$reports = xarModAPIFunc('reports','user','report_getall',array());
    
    // Include connection info for each report
    foreach ($reports as $key => $report) {
        $reports[$key]['name']=xarVarPrepForDisplay($reports[$key]['name']);
        $reports[$key]['connection'] = xarModAPIFunc('reports','user','connection_get',array('conn_id'=>$report['conn_id']));
    }

    $data['reports']=$reports;
    return $data;
}

?>