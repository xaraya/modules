<?php
/**
 * Display a list of defined connections
 *
 */
function reports_admin_view_connections() {
	// Get a list of connections
	$connections = xarModAPIFunc('reports','user','connection_getall',array());

    // FIXME: it shouldn't be necessary to prep here
    foreach ($connections as $key => $connection) {
        $connections[$key]['name']=xarVarPrepForDisplay($connections[$key]['name']);
        $connections[$key]['description']=xarVarPrepForDisplay($connections[$key]['description']);
    }

    $data['connections']=$connections;
    return $data;
}

/*
 * Display a list of defined reports
 */
function reports_admin_view_reports() {
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