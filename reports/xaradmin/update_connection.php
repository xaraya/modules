<?php
/**
 * Pass update to admin api
 */
function reports_admin_update_connection($args) 
{
	list($conn_name, $conn_desc,$conn_type,$conn_server,$conn_database,$conn_user,$conn_password,$conn_id) = 
		xarVarCleanFromInput('conn_name','conn_desc','conn_type','conn_server','conn_database','conn_user','conn_password','conn_id');
	extract($args);
    
	// Only desc, user and password may be empty, rest must have values
    
	// Confirm authorization key
    if (!xarSecConfirmAuthKey()) {
        return false;
    } else {
        if (!xarModAPIFunc('reports','admin','update_connection',array('conn_name'=>$conn_name,'conn_desc'=>$conn_desc,	
                                                                       'conn_type'=>$conn_type,'conn_server'=>$conn_server,
                                                                       'conn_database'=>$conn_database,'conn_user'=>$conn_user,
                                                                       'conn_password'=>$conn_password,'conn_id'=>$conn_id))) {
            // Create failed
            xarSessionSetVar('errormsg', xarML("Update connection failed"));
        }
    }
	    
	// Redisplay the connection screen (thus showing the newly added connection
	xarResponseRedirect(xarModUrl('reports','admin','view_connections',array()));
    
	return true;
}

?>