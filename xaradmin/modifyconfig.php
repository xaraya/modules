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
 * Update the configuration parameters of the module based on data from the modification form
 *
 * @author mikespub
 * @access public
 * @param no $ parameters
 * @return true on success or void on failure
 * @throws no exceptions
 * @todo nothing
 */
function workflow_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurity::check('AdminWorkflow')) {
        return;
    }

    $data = [];
    $data['settings'] = [];

    $data['module_settings'] = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'blocks']);
    $data['module_settings']->getItem();

    $create = xarModVars::get('workflow', 'default.create');
    $update = xarModVars::get('workflow', 'default.update');
    $delete = xarModVars::get('workflow', 'default.delete');
    $data['settings']['default'] = ['label' => xarML('Default configuration'),
                                         'create' => $create,
                                         'update' => $update,
                                         'delete' => $delete, ];

    $hookedmodules = xarMod::apiFunc(
        'modules',
        'admin',
        'gethookedmodules',
        ['hookModName' => 'workflow']
    );
    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $modname => $value) {
            // we have hooks for individual item types here
            if (!isset($value[0])) {
                // Get the list of all item types for this module (if any)
                $mytypes = xarMod::apiFunc(
                    $modname,
                    'user',
                    'getitemtypes',
                                         // don't throw an exception if this function doesn't exist
                                         [],
                    0
                );
                foreach ($value as $itemtype => $val) {
                    $create = xarModVars::get('workflow', "$modname.$itemtype.create");
                    if (empty($create)) {
                        $create = '';
                    }
                    $update = xarModVars::get('workflow', "$modname.$itemtype.update");
                    if (empty($update)) {
                        $update = '';
                    }
                    $delete = xarModVars::get('workflow', "$modname.$itemtype.delete");
                    if (empty($delete)) {
                        $delete = '';
                    }
                    if (isset($mytypes[$itemtype])) {
                        $type = $mytypes[$itemtype]['label'];
                        $link = $mytypes[$itemtype]['url'];
                    } else {
                        $type = xarML('type #(1)', $itemtype);
                        $link = xarController::URL($modname, 'user', 'view', ['itemtype' => $itemtype]);
                    }
                    $data['settings']["$modname.$itemtype"] = ['label' => xarML('Configuration for #(1) module - <a href="#(2)">#(3)</a>', $modname, $link, $type),
                                                                    'create' => $create,
                                                                    'update' => $update,
                                                                    'delete' => $delete, ];
                }
            } else {
                $create = xarModVars::get('workflow', "$modname.create");
                if (empty($create)) {
                    $create = '';
                }
                $update = xarModVars::get('workflow', "$modname.update");
                if (empty($update)) {
                    $update = '';
                }
                $delete = xarModVars::get('workflow', "$modname.delete");
                if (empty($delete)) {
                    $delete = '';
                }
                $link = xarController::URL($modname, 'user', 'main');
                $data['settings'][$modname] = ['label' => xarML('Configuration for <a href="#(1)">#(2)</a> module', $link, $modname),
                                                    'create' => $create,
                                                    'update' => $update,
                                                    'delete' => $delete, ];
            }
        }
    }

    // Common setup for Galaxia environment
    sys::import('modules.workflow.lib.galaxia.config');
    include_once(GALAXIA_LIBRARY.'/processmonitor.php');

    // get all start activities that are not interactive
    $activities = $processMonitor->monitor_list_activities(0, -1, 'pId_asc', '', "type='start' and isInteractive='n'");

    // get the name of all processes
    $all_procs = $processMonitor->monitor_list_all_processes('pId_asc', "isActive='y'");
    $pid2name = [];
    foreach ($all_procs as $info) {
        $pid2name[$info['pId']] = $info['name'] . ' ' . $info['version'];
    }

    // build a list of activity ids and names
    $data['activities'] = [];
    $data['activities'][0] = '';
    foreach ($activities['data'] as $info) {
        if (isset($pid2name[$info['pId']])) {
            $data['activities'][$info['activityId']] = $pid2name[$info['pId']] . ' - ' . $info['name'];
        }
    }

    // get all stand-alone activities that are not interactive
    $activities = $processMonitor->monitor_list_activities(0, -1, 'pId_asc', '', "type='standalone' and isInteractive='n'");

    // build a list of activity ids and names
    $data['standalone'] = [];
    foreach ($activities['data'] as $info) {
        if (isset($pid2name[$info['pId']])) {
            $data['standalone'][$info['activityId']] = $pid2name[$info['pId']] . ' - ' . $info['name'];
        }
    }

    // We need to keep track of our own set of jobs here, because the scheduler won't know what
    // workflow activities to run when. Other modules will typically have 1 job that corresponds
    // to 1 API function, so they won't need this...

    $serialjobs = xarModVars::get('workflow', 'jobs');
    if (!empty($serialjobs)) {
        $data['jobs'] = unserialize($serialjobs);
    } else {
        $data['jobs'] = [];
    }
    $data['jobs'][] = ['activity' => '',
                            'interval' => '',
                            'lastrun' => '',
                            'result' => '', ];

    if (xarMod::isAvailable('scheduler')) {
        $data['intervals'] = xarMod::apiFunc('scheduler', 'user', 'intervals');
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
            $data['interval'] = '';
        } else {
            $data['interval'] = $job['interval'];
        }
    } else {
        $data['intervals'] = [];
        $data['interval'] = '';
    }

    $data['authid'] = xarSec::genAuthKey();
    return $data;
}
