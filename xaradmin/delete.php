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
 * delete a scheduler job
 *
 * @author mikespub
 * @param  $args ['itemid'] job id
 * @return true on success, void on failure
 */
function scheduler_admin_delete()
{
    // Get parameters
    if (!xarVar::fetch('itemid', 'id', $itemid)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'str:1:', $confirm, '', xarVar::NOT_REQUIRED)) {
        return;
    }

    // Security Check
    if (!xarSecurity::check('AdminScheduler')) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $job = DataObjectMaster::getObject(['name' => 'scheduler_jobs']);

    // Check for confirmation
    if (empty($confirm)) {
        // No confirmation yet - get the item
        $job->getItem(['itemid' => $itemid]);

        if (empty($job)) {
            $msg = xarML(
                'Job #(1) for #(2) function #(3)() in module #(4)',
                $itemid,
                'admin',
                'delete',
                'scheduler'
            );
            throw new Exception($msg);
        }

        $data['authid'] = xarSec::genAuthKey();
        $data['triggers'] = xarMod::apiFunc('scheduler', 'user', 'triggers');
        $data['job'] = $job;
        $data['properties'] = $job->properties;
        $data['itemid'] = $itemid;
        return $data;
    }

    // Confirm Auth Key
    if (!xarSec::confirmAuthKey()) {
        return;
    }

    $job->deleteItem(['itemid' => $itemid]);
    // Pass to API
    xarController::redirect(xarController::URL('scheduler', 'admin', 'view'));
    return true;
}
