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
 * Modify extra information for scheduler jobs
 * @param id itemid
 */
function scheduler_admin_new()
{
    if (!xarSecurity::check('AdminScheduler')) {
        return;
    }

    if (!xarVar::fetch('confirm', 'isset', $confirm, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('addjob', 'str', $addjob, '', xarVar::NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => 'scheduler_jobs']);

    if (!empty($addjob) && preg_match('/^(\w+);(\w+);(\w+)$/', $addjob, $matches)) {
        $data['object']->properties['module']->value = $matches[1];
        $data['object']->properties['type']->value = $matches[2];
        $data['object']->properties['function']->value = $matches[3];
    }

    if (!empty($confirm)) {
        $isvalid = $data['object']->checkInput();

        /*if ($job_interval == '0c' && !empty($config['crontab'])) {
            $config['crontab']['nextrun'] = xarMod::apiFunc('scheduler','user','nextrun',
                                                          $config['crontab']);
        }
        $job['config'] = $config;*/

        if (!$isvalid) {
            var_dump($data['object']->getInvalids());
            exit;
            xarController::redirect(xarController::URL('scheduler', 'admin', 'new'));
        }

        $itemid = $data['object']->createItem();
        xarController::redirect(xarController::URL('scheduler', 'admin', 'view'));
        return true;
    }
    return $data;
}
