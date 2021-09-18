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
 */
/**
 * Test a scheduler job
 *
 * @author mikespub
 * @param  $args ['itemid'] job id
 * @return true on success, void on failure
 */
function scheduler_admin_test()
{
    // Get parameters
    if (!xarVar::fetch('itemid', 'id', $itemid, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'str:1:', $confirm, '', xarVar::NOT_REQUIRED)) {
        return;
    }

    if (empty($itemid)) {
        return xarResponse::NotFound();
    }

    // Security Check
    if (!xarSecurity::check('AdminScheduler')) {
        return;
    }

    // Check for confirmation
    if (empty($confirm)) {
        // No confirmation yet - display the confirmation page
        $data['itemid'] = $itemid;
        return $data;
    }

    // Confirm Auth Key
    if (!xarSec::confirmAuthKey()) {
        return;
    }

    // Get the job details
    sys::import('modules.dynamicdata.class.objects.master');
    $job = DataObjectMaster::getObject(['name' => 'scheduler_jobs']);
    $job->getItem(['itemid' => $itemid]);

    // Run the job
    $result = xarMod::apiFunc(
        $job->properties['module']->value,
        $job->properties['type']->value,
        $job->properties['function']->value
    );

    // Go back to the view page
    xarController::redirect(xarController::URL('scheduler', 'admin', 'view'));
    return true;
}
