<?php
/**
 * Pass update to admin api
 */
function reports_admin_update_report($args) 
{
	list($rep_id, $rep_name, $rep_desc,$rep_conn,$rep_xmlfile) = 
		xarVarCleanFromInput('rep_id','rep_name','rep_desc','rep_conn_id','rep_xmlfile');
	extract($args);
    
	// Only desc, user and password may be empty, rest must have values
    
    if (!xarSecConfirmAuthKey()) {
        return false;
        // TODO: exception?
    } else {
        if (!xarModAPIFunc('reports',
                           'admin',
                           'update_report',
                           array('rep_id'=>$rep_id, 'rep_name'=>$rep_name,
                                 'rep_desc'=>$rep_desc,'rep_conn'=>$rep_conn,
                                 'rep_xmlfile'=>$rep_xmlfile
                                 )
                           )
            ) {
            // Create failed
            return false;
            // TODO: exception?
        }
    }
    
	// Redisplay the connection screen (thus showing the newly added connection
	xarResponseRedirect(xarModUrl('reports','admin','view_reports',array()));
    return true;
}

?>