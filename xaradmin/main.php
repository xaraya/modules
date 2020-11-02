<?php
/**
 * Scheduler Module
 *
 * @package modules
 * @subpackage scheduler module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * the main administration function
 */
function scheduler_admin_main()
{
    if (!xarSecurity::check('AdminScheduler')) return;

    if (xarModVars::get('modules', 'disableoverview') == 0){
        return array();
    } else {
        xarController::redirect(xarController::URL('scheduler', 'admin', 'view'));
    }
    return true;
}

?>
