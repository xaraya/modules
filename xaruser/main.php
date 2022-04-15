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
 * the main user function - only used for external triggers
 * @param  $args ['itemid'] job id (optional)
 */
function scheduler_user_main()
{
    // Check when we last ran the scheduler
    $lastrun = xarModVars::get('scheduler', 'lastrun');
    $now = time();

    $interval = xarModVars::get('scheduler', 'interval');		// The interval is set in modifyconfig
    if (!empty($interval)) {
        if (!empty($lastrun) && $lastrun >= $now - $interval) {  // Make sure the defined interval has passed
            $diff = time() - $lastrun;
            $data['message'] = xarML('Last run was #(1) minutes #(2) seconds ago', intval($diff / 60), $diff % 60);
            return $data;
        }
        // Update the last run time
        xarModVars::set('scheduler', 'lastrun', $now);
    }

    xarModVars::set('scheduler', 'running', 1);
    $data['output'] = xarMod::apiFunc('scheduler', 'user', 'runjobs');
    xarModVars::delete('scheduler', 'running');

    if (xarModVars::get('scheduler', 'debugmode') && in_array(xarUser::getVar('id'), xarConfigVars::get(null, 'Site.User.DebugAdmins'))) {
        // Show the output only to administrators
        return $data;
    } else {
        // Everyone else gets turned away
        return xarController::$response->NotFound();
    }
}
