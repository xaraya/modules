<?php
/**
 * Produce a list of available reports
 */
function reports_user_view($args=array()) 
{
    
    // Produce table with report info
    $reportlist= xarModApiFunc('reports','user','report_getall');
  
	// End the output
 	$data['reportlist']=$reportlist;
    return $data;
}
?>