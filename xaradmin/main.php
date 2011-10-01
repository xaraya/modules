<?php
/**
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 *
 * @subpackage shouter
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