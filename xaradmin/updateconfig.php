<?php
/**
 * Update site configuration
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage wizards
 * @author Marc Lutolf
 */
/**
 * Update site configuration
 *
 * @param string
 * @return void?
 */
function wizards_admin_updateconfig()
{
    if (!xarVarFetch('adminwizards','int',$adminwizards)) return;
    if (!xarVarFetch('userwizards','int',$userwizards)) return;

    // Security Check
    if(!xarSecurityCheck('AdminWizard')) return;

    $wizards = $adminwizards * 2 + $userwizards;
    xarModSetVar('wizards','status',$wizards);
    xarResponseRedirect(xarModURL('wizards', 'admin', 'modifyconfig'));

    return true;
}

?>
