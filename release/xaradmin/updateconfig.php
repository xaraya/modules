<?php
/**
 * File: $Id:
 * 
 * Update configuration parameters of the module with information passed back by the modification form
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @author Release module development team 
 */
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function release_admin_updateconfig()
{
    // Get parameters from whatever input we need.
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    // Update module variables.
    xarModSetVar('release', 'SupportShortURLs', $shorturls);

    xarModCallHooks('module','updateconfig','release',
                   array('module' => 'release'));

    xarResponseRedirect(xarModURL('release', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>