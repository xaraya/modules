<?php
/**
 * Update the configuration parameters of the module based on data from the modification form
 *
 * @author  John Cox <niceguyeddie@xaraya.com>
 * @access  public
 * @param   no parameters
 * @return  true on success or void on failure
 * @throws  no exceptions
 * @todo    nothing
*/
function paypalipn_admin_updateconfig()
{ 
	// Get parameters
	if (!xarVarFetch('testmode', 'checkbox', $testmode, false, XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('email', 'checkbox', $email, false, XARVAR_NOT_REQUIRED)) return;
	// Confirm authorisation code
	if (!xarSecConfirmAuthKey()) return; 
	// update the data
    xarModSetVar('paypalipn', 'email', $email);
    xarModSetVar('paypalipn', 'testmode', $testmode);
	// lets update status and display updated configuration
	xarResponseRedirect(xarModURL('paypalipn', 'admin', 'modifyconfig')); 
	// Return
	return true;
} 
?>