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
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @return array containing the item types and their description
 */
function workflow_userapi_getitemtypes($args)
{
    $itemtypes = [];

    // Common setup for Galaxia environment
    sys::import('modules.workflow.lib.galaxia.config');
    include(GALAXIA_LIBRARY.'/processmonitor.php');

    // get all active processes
    $processes = $processMonitor->monitor_list_all_processes('name_asc', "isActive = 1");

    foreach ($processes as $process) {
        $itemtypes[$process['pId']] = ['label' => xarVar::prepForDisplay($process['name'] . ' ' . $process['version']),
                                            'title' => xarVar::prepForDisplay(xarML('View Process')),
                                            'url'   => xarController::URL(
                                                'workflow',
                                                'user',
                                                'activities',
                                                ['filter_process' => $process['pId']]
                                            ),
                                           ];
    }
    return $itemtypes;
}
