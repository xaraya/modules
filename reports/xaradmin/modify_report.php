<?php
/** 
 * Modify a report
 */
function reports_admin_modify_report($args) 
{
	list($rep_id) = xarVarCleanFromInput('rep_id');
	extract($args);
    
	$rep = xarModAPIFunc('reports','user','report_get',array('rep_id'=>$rep_id));
	$connections= xarModAPIFunc('reports','user','connection_getall',array());
    $data=array ('authid' => xarSecGenAuthKey(),
                 'updatelabel' => xarML('Update Report'),
                 'rep_id' => $rep_id,
                 'name' => $rep['name'],
                 'description' => $rep['description'],
                 'xmlfile' => $rep['xmlfile'],
                 'rep_conn_id' => $rep['conn_id'],
                 'connections' => $connections
                 );
	return $data;
}

?>