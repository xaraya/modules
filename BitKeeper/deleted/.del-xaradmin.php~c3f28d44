<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 * 
 * paypalsetup System
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * @subpackage paypalsetup module
 * @author John Cox <admin@dinerminor.com> 
 */

/**
 * the main administration function
 *
 * @author  John Cox <niceguyeddie@xaraya.com>
 * @access  public
 * @param   no parameters
 * @return  true on success or void on falure
 * @throws  XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
*/
function paypalipn_admin_main()
{
	// Security Check
	if (!xarSecurityCheck('AdminPayPalIPN')) return;
    return array();
}

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