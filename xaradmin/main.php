<?php
/*
 * Censor Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage  Censor Module
 * @author John Cox
*/

/**
 * Add a standard screen upon entry to the module.
 * 
 * @returns output
 * @return output with censor Menu information
 */
function censor_admin_main()
{ 
    // Security Check
    if (!xarSecurityCheck('EditCensor')) return; 
    // we only really need to show the default view (overview in this case)
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('censor', 'admin', 'view'));
    } 
    // success
    return true;
} 


?>