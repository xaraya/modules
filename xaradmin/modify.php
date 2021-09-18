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
function scheduler_admin_modify()
{
    if (!xarSecurity::check('AdminScheduler')) {
        return;
    }

    if (!xarVar::fetch('confirm', 'isset', $confirm, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'id', $data['itemid'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }

    if (empty($data['itemid'])) {
        xarController::redirect(xarController::URL('scheduler', 'admin', 'view'));
        return true;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => 'scheduler_jobs']);
    $data['object']->getItem(['itemid' => $data['itemid']]);

    if (!empty($confirm)) {
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        $isvalid = $data['object']->checkInput();

        if (!$isvalid) {
            var_dump($data['object']->getInvalids());
            exit;
            xarController::redirect(xarController::URL('scheduler', 'admin', 'modify', ['itemid' => $itemid]));
        }

        $itemid = $data['object']->updateItem(['itemid' => $data['itemid']]);

        xarController::redirect(xarController::URL('scheduler', 'admin', 'view'));
        return true;

        if (!xarVar::fetch('config', 'isset', $config, [], xarVar::NOT_REQUIRED)) {
            return;
        }
        if (empty($config)) {
            $config = [];
        }
        if ($interval == '0c' && !empty($config['crontab'])) {
            $config['crontab']['nextrun'] = xarMod::apiFunc(
                'scheduler',
                'user',
                'nextrun',
                $config['crontab']
            );
        }
        $job['config'] = $config;

        $serialjobs = serialize($jobs);
        xarModVars::set('scheduler', 'jobs', $serialjobs);
    }


    // Prefill the configuration array
    if (empty($data['config'])) {
        $data['config'] = [
                                'params' => '',
                                'startdate' => '',
                                'enddate' => '',
                                'crontab' => ['minute' => '',
                                                   'hour' => '',
                                                   'day' => '',
                                                   'month' => '',
                                                   'weekday' => '',
                                                   'nextrun' => '', ],
                                // not supported yet
                                'runas' => ['user' => '',
                                                 'pass' => '', ],
                               ];
    }

    return $data;
}
