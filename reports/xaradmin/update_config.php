<?php
/**
 * Update configuration
 */
function reports_admin_update_config($args) 
{
	// Get parameters
    xarVarFetch('config_replocation','str::',$config_replocation);
    xarVarFetch('config_imglocation','str::',$config_imglocation);
    xarVarFetch('config_pdfbackend','str::',$config_pdfbackend);
    xarVarFetch('config_defaultoutput','str::',$config_defaultoutput);

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
                                 'config_pdfbackend'=>$config_pdfbackend,
                                 'config_defaultoutput' => $config_defaultoutput
                                 )
                           )
            ) {
            return false;
        }
    }
		
	xarResponseRedirect(xarModURL('reports', 'admin', 'modify_config'));
	return true;
	
}
?>