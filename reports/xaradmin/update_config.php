<?php
/**
 * Update configuration
 */
function reports_admin_update_config($args) 
{
	// Get parameters
	list($config_replocation, $config_imglocation, $config_pdfbackend) =
		xarVarCleanFromInput('config_replocation','config_imglocation','config_pdfbackend');
	extract($args);
	
	if (!xarSecConfirmAuthKey()) {
		//TODO: exception
        return false;
	} else {
        // Do the actual work
        if (!xarModAPIFunc('reports',
                           'admin',
                           'update_config',
                           array('config_replocation'=>$config_replocation,
                                 'config_imglocation'=>$config_imglocation,
                                 'config_pdfbackend'=>$config_pdfbackend
                                 )
                           )
            ) {
            return false;
        }
    }
		
	xarResponseRedirect(xarModURL('reports', 'admin', 'main'));
	return true;
	
}
?>