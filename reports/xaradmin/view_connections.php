<?php
/**
 * Display a list of defined connections
 *
 */
function reports_admin_view_connections() 
{
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
?>