<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
/**
 * Update configuration
 */
function workflow_admin_updateconfig()
{
    // Get parameters
    xarVar::fetch('settings', 'isset', $settings, '', xarVar::DONT_SET);

    // Confirm authorisation code
    if (!xarSec::confirmAuthKey()) {
        return;
    }
    // Security Check
    if (!xarSecurity::check('AdminWorkflow')) {
        return;
    }

    if (!xarVar::fetch('jobs', 'isset', $jobs, [], xarVar::NOT_REQUIRED)) {
        return;
    }
    if (empty($jobs)) {
        $jobs = [];
    }
    $savejobs = [];
    foreach ($jobs as $job) {
        if (!empty($job['activity']) && !empty($job['interval'])) {
            $savejobs[] = $job;
        }
    }
    $serialjobs = serialize($savejobs);
    xarModVars::set('workflow', 'jobs', $serialjobs);

    if (xarMod::isAvailable('scheduler')) {
        if (!xarVar::fetch('interval', 'str:1', $interval, '', xarVar::NOT_REQUIRED)) {
            return;
        }
        // see if we have a scheduler job running to execute workflow activities
        $job = xarMod::apiFunc(
            'scheduler',
            'user',
            'get',
            ['module' => 'workflow',
                                   'type' => 'scheduler',
                                   'func' => 'activities', ]
        );
        if (empty($job) || empty($job['interval'])) {
            if (!empty($interval)) {
                // create a scheduler job
                xarMod::apiFunc(
                    'scheduler',
                    'admin',
                    'create',
                    ['module' => 'workflow',
                                    'type' => 'scheduler',
                                    'func' => 'activities',
                                    'interval' => $interval, ]
                );
            }
        } elseif (empty($interval)) {
            // delete the scheduler job
            xarMod::apiFunc(
                'scheduler',
                'admin',
                'delete',
                ['module' => 'workflow',
                                'type' => 'scheduler',
                                'func' => 'activities', ]
            );
        } elseif ($interval != $job['interval']) {
            // update the scheduler job
            xarMod::apiFunc(
                'scheduler',
                'admin',
                'update',
                ['module' => 'workflow',
                                'type' => 'scheduler',
                                'func' => 'activities',
                                'interval' => $interval, ]
            );
        }
    }

    $data['module_settings'] = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'blocks']);
    $data['module_settings']->getItem();
    $isvalid = $data['module_settings']->checkInput();
    if (!$isvalid) {
        return xarTpl::module('workflow', 'admin', 'modifyconfig', $data);
    } else {
        $itemid = $data['module_settings']->updateItem();
    }

    xarController::redirect(xarController::URL('workflow', 'admin', 'modifyconfig'));

    return true;
}
