<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

/**
 * The main administration function
 * This function redirects the user to the view function
 * @return bool true
 */
function uploads_admin_main()
{
    if (!xarSecurity::check('EditUploads')) {
        return;
    }

    if (xarModVars::get('modules', 'disableoverview') == 0) {
        return [];
    } else {
        xarController::redirect(xarController::URL('uploads', 'admin', 'view'));
    }
    // success
    return true;
}
