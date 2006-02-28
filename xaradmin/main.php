<?php
/**
 * Xaraya Autolinks main admin function
 *
 * @package modules
 * @copyright (C) 2002-2006 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Autolinks Module
 * @link http://xaraya.com/index.php/release/11.html
 * @author Jason Judge/Jim McDonald
*/
/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with Autolinks Menu information
 */
function autolinks_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('EditAutolinks')) return;
       xarResponseRedirect(xarModURL('autolinks', 'admin', 'view'));
    // success
    return true;
}

?>