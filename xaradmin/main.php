<?php
/**
 * Ratings Module
 *
 * @package modules
 * @subpackage ratings module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
function ratings_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('ManageRatings')) return;

    if (xarModVars::get('modules', 'disableoverview') == 0) {
        return array();
    } else {
        xarController::redirect(xarModURL('ratings', 'admin', 'view'));
    }
    // success
    return true;
}

?>