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
 * List modules and current settings
 * @param several params from the associated form in template
 *
 */
function censor_admin_settings()
{
    // Security Check
    if(!xarSecurityCheck('EditCensor')) return;
    if (!xarVarFetch('selstyle', 'str:1:', $selstyle, 'plain', XARVAR_NOT_REQUIRED)) return;
    xarModSetVar('censor', 'selstyle', $selstyle);
    xarResponseRedirect(xarModURL('censor', 'admin', 'view'));
    return true;
}
?>