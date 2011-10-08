<?php
/**
 * Shouter Module
 *
 * @package modules
 * @subpackage shouter module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */

/**
 * Show main administration page
 */
function shouter_admin_main()
{

    if (!xarSecurityCheck('EditShouter')) return;
    if (xarModVars::get('modules', 'disableoverview') == 0){
        $data = xarModAPIFunc('shouter', 'admin', 'menu');

        return $data;
    } else {
        xarController::redirect(xarModURL('shouter', 'admin', 'view'));
    }

    return true;
}
?>