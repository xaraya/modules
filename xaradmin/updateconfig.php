<?php
/**
 * Change Log Module version information
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage changelog
 * @link http://xaraya.com/index.php/release/185.html
 * @author mikespub
 */
/**
 * Update configuration
 */
function changelog_admin_updateconfig()
{
    // Get parameters
    // FIXME: xarVarFetch validation? array? isset?
    $changelog = xarVarCleanFromInput('changelog');

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;
    // Security Check
    if (!xarSecurityCheck('AdminChangeLog')) return;

    if (isset($changelog) && is_array($changelog)) {
        foreach ($changelog as $modname => $value) {
            if ($modname == 'default') {
                xarModSetVar('changelog', 'default', $value);
            } else {
                xarModSetVar('changelog', $modname, $value);
            }
        }
    }

    if (!xarVarFetch('numstats', 'int', $numstats, 100, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showtitle', 'checkbox', $showtitle, false, XARVAR_NOT_REQUIRED)) return;
    xarModSetVar('changelog', 'numstats', $numstats);
    xarModSetVar('changelog', 'showtitle', $showtitle);

    xarResponseRedirect(xarModURL('changelog', 'admin', 'modifyconfig'));

    return true;
}

?>
