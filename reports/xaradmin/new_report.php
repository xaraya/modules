<?php
/**
 * Show form to define a new report
 */
function reports_admin_new_report() 
{
    // Get all connections
    $connections = xarModAPIFunc('reports','user','connection_getall',array());
	
    $data = array ('rep_id' => 0,
                   'authid' => xarSecGenAuthKey(),
                   'name' => '(untitled report)',
                   'description' => 'no description',
                   'xmlfile' => 'empty.xml',
                   'rep_conn_id' => 0,
                   'createlabel' => xarML('Create report'),
                   'connections' => $connections
                   );
    return $data;
}

?>