<?php
/**
 * File: $Id: s.xarinit.php 1.11 03/01/18 11:39:31-05:00 John.Cox@mcnabb. $
 * 
 * Xaraya Referers
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 * @subpackage Referer Module
 * @author John Cox et al. 
 */

/**
 * delete item
 * 
 * @param  $ 'exid' the id of the item to be deleted
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function referer_admin_delete($args)
{ 
    // Get parameters from whatever input we need.
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return; 
    // Security Check
    if (!xarSecurityCheck('DeleteReferer')) return; 
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return; 
    // The API function is called.
    if (!xarModAPIFunc('referer',
            'admin',
            'delete')) {
        return; // throw back
    } 

    xarResponseRedirect(xarModURL('referer', 'admin', 'view')); 
    // Return
    return true;
} 

?>