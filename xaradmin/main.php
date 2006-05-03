<?php
/**
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
 * the main administration function
 * This function is the default function, and is called whenever the
 * module is initiated without defining arguments.
 */
function referer_admin_main()
{ 
    // Security Check
    if (!xarSecurityCheck('EditReferer')) return; 
    // we only really need to show the default view (overview in this case)

    xarResponseRedirect(xarModURL('referer', 'admin', 'view'));

    // success
    return true;
} 

?>