<?php
/**
 * Gather entered info and let admin api process new report creation
 */
function reports_admin_create_report($args) 
{
	list($rep_id, $rep_name, $rep_desc,$rep_xmlfile, $rep_conn_id) = 
		xarVarCleanFromInput('rep_id','rep_name','rep_desc','rep_xmlfile', 'rep_conn_id');
	extract($args);
    
	// Only desc, user and password may be empty, rest must have values
    if (!xarSecConfirmAuthKey()) {
        // TODO: exception?
        return false;
    } else {
        if (!xarModAPIFunc('reports',
                           'admin',
                           'create_report',
                           array('rep_id'=>$rep_id,
                                 'rep_name'=>$rep_name,
                                 'rep_desc'=>$rep_desc,
                                 'rep_xmlfile'=>$rep_xmlfile,
                                 'rep_conn_id'=>$rep_conn_id
                                 )
                           )
            ) { 
            // Create failed
            xarSessionSetVar('errormsg', xarML("Create report failed"));
        }
    }
	
	// Go back to reports menu and display status and or errors
	xarResponseRedirect(xarModUrl('reports','admin','view_reports',array()));
	return true;
}

?>