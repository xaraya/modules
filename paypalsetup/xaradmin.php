<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 * 
 * paypalsetup System
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * @subpackage paypalsetup module
 * @author John Cox <niceguyeddie@xaraya.com> 
 */

/**
 * the main administration function
 *
 * @author  John Cox <niceguyeddie@xaraya.com>
 * @access  public
 * @return  true on success or void on falure
 * @throws  XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
*/
function paypalsetup_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('AdminPayPalSetUp')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('paypalsetup', 'admin', 'modifyconfig'));
    } 
    // success
    return true;
} 

/**
 * Update the configuration parameters of the module based on data from the modification form
 *
 * @author  John Cox <niceguyeddie@xaraya.com>
 * @access  public
 * @return  array
 * @throws  no exceptions
 * @todo    nothing
*/
function paypalsetup_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminPayPalSetUp')) return; 
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
 * @return  true on success or void on failure
 * @throws  no exceptions
 * @todo    nothing
*/
function paypalsetup_admin_updateconfig()
{ 
    $business_default = xarModGetVar('mail', 'adminmail');
    $url_default = xarServerGetBaseURL();
    // Get parameters
    if (!xarVarFetch('currency_code', 'str:1:', $currency, 'USD', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('business', 'str:1:', $business, $business_default, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return', 'str:1:', $return, $url_default, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cancel_return', 'str:1:', $cancel_return, $url_default, XARVAR_NOT_REQUIRED)) return;
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return; 
    // update the data
    xarModSetVar('paypalsetup', 'currency_code', $currency);
    xarModSetVar('paypalsetup', 'business', $business);
    xarModSetVar('paypalsetup', 'return', $return);
    xarModSetVar('paypalsetup', 'cancel_return', $cancel_return);

    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('paypalsetup', 'admin', 'modifyconfig')); 
    // Return
    return true;
} 

?>