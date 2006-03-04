<?php
/*
 * Censor Module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage  Censor Module
 * @author John Cox
*/

/**
 * update module configuration
 */
function censor_admin_updateconfig()
{
    // Get parameters
    if (!xarVarFetch('itemsperpage', 'int:1:', $itemsperpage, '20', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('replace', 'str:1:', $replace, '******', XARVAR_NOT_REQUIRED)) return;
    if (!xarSecConfirmAuthKey()) return;
    // Security Check
    if (!xarSecurityCheck('AdminCensor')) return;

    xarModSetVar('censor', 'replace', $replace);
    xarModSetVar('censor', 'itemsperpage', $itemsperpage);
    xarResponseRedirect(xarModURL('censor', 'admin', 'modifyconfig'));
    return true;
}
?>