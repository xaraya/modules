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
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function referer_admin_updateconfig()
{ 
    // Get parameters
    if (!xarVarFetch('itemsperpage', 'int:1:', $itemsperpage, '100', XARVAR_NOT_REQUIRED)) return; 
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return; 
    // Security Check
    if (!xarSecurityCheck('AdminReferer')) return; 
    // update the data
    if (empty($itemsperpage)) {
        $itemsperpage = 100;
    } 

    xarModSetVar('referer', 'itemsperpage', $itemsperpage); 
    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('referer', 'admin', 'modifyconfig')); 
    // Return
    return true;
} 

?>