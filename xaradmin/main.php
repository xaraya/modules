<?php
/**
 * Add a standard screen upon entry to the module
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/

/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with xarbb Menu information
 */
function xarbb_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('EditxarBB',1,'Forum')) return;

       xarResponseRedirect(xarModURL('xarbb', 'admin', 'view'));

    // success
    return true;
}

?>
