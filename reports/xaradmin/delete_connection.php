<?php
/**
 * Process a delete request for connections
 */
function reports_admin_delete_connection($args) 
{
	list($conn_id) = xarVarCleanFromInput('conn_id');
	extract($args);
    
    if (!xarModAPIFunc('reports','admin','delete_connection',array('conn_id'=>$conn_id))) {
        xarSessionSetVar('errormsg',xarML("Delete connection failed"));
    }
	
	xarResponseRedirect(xarModUrl('reports','admin','view_connections',array()));
}

?>