<?php
/**
 * Process a delete request for reports
 */
function reports_admin_delete_report($args) 
{
	list($rep_id) = xarVarCleanFromInput('rep_id');
	extract($args);
    
    if (!xarModAPIFunc('reports','admin','delete_report',array('rep_id'=>$rep_id))) {
        return false;
    }
	
	xarResponseRedirect(xarModUrl('reports','admin','view_reports',array()));
	return true;
}
?>