<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 * 
 * PayPal IPN
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * @subpackage paypalsetup module
 * @author John Cox <niceguyeddie@xaraya.com> 
 */

function paypalipn_init()
{
    xarRegisterMask('AdminPayPalIPN', 'All', 'paypalipn', 'All', 'All', 'ACCESS_ADMIN');
    return true;
} 

function paypalipn_delete()
{
    // Remove Masks and Instances
    xarRemoveMasks('paypalipn');
    xarRemoveInstances('paypalipn');
    return true;
}

?>