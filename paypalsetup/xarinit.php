<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 * 
 * PayPal Set-up
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * @subpackage paypalsetup module
 * @author John Cox <niceguyeddie@xaraya.com> 
 */

function paypalsetup_init()
{
    $business = xarModGetVar('mail', 'adminmail');
    $return = xarServerGetBaseURL();
    xarModSetVar('paypalsetup', 'currency_code', 'USD');
    xarModSetVar('paypalsetup', 'business', $business);
    xarModSetVar('paypalsetup', 'return', $return);
    xarRegisterMask('AdminPayPalSetUp', 'All', 'paypalsetup', 'All', 'All', 'ACCESS_ADMIN');
    return true;
} 

function paypalsetup_delete()
{

    // Remove Masks and Instances
    xarModDelAllVars('paypalsetup');
    xarRemoveMasks('paypalsetup');
    xarRemoveInstances('paypalsetup');
    return true;
} 
?>