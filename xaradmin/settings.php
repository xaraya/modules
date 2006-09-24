<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * List modules and current settings
 * @param several params from the associated form in template
 *
 */
function headlines_admin_settings()
{
    // Security Check
    if(!xarSecurityCheck('EditHeadlines')) return;
    if (!xarVarFetch('selstyle', 'str:1:', $selstyle, 'plain', XARVAR_NOT_REQUIRED)) return;
    xarModSetVar('headlines', 'selstyle', $selstyle);
    xarResponseRedirect(xarModURL('headlines', 'admin', 'view'));
    return true;
}
?>