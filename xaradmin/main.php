<?php
/**
 * Hitcount Module
 *
 * @package modules
 * @subpackage hitcount module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */
/**
 * Add a standard screen upon entry to the module.
 * @return bool true on success of redirect
 */
function hitcount_admin_main()
{
    if (!xarSecurity::check('ManageHitcount')) {
        return;
    }

    if (xarModVars::get('modules', 'disableoverview') == 0) {
        return array();
    } else {
        xarController::redirect(xarController::URL('hitcount', 'admin', 'view'));
    }
    return true;
}
