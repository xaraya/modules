<?php
/**
 * Update the configuration parameters of the module based on data from the modification form
 *
 * @author  John Cox <niceguyeddie@xaraya.com>
 * @access  public
 * @param   no parameters
 * @return  array();
 * @throws  no exceptions
 * @todo    nothing
*/
function paypalipn_admin_modifyconfig()
{
	// Security Check
	//if (!xarSecurityCheck('AdminPayPalSetUp')) return; 
	// Generate a one-time authorisation code for this operation
	$data['authid'] = xarSecGenAuthKey(); 
    $data['createlabel'] = xarML('Submit');
	return $data;
} 
?>