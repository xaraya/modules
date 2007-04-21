<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
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
    $itemtypes = array();

    // Common setup for Galaxia environment
    sys::import('modules.workflow.lib.galaxia.config');
    include(GALAXIA_LIBRARY.'/processmonitor.php');

    // get all active processes
    $processes = $processMonitor->monitor_list_all_processes('name_asc', "isActive = 'y'");

    foreach ($processes as $process) {
        $itemtypes[$process['pId']] = array('label' => xarVarPrepForDisplay($process['name'] . ' ' . $process['version']),
                                            'title' => xarVarPrepForDisplay(xarML('View Process')),
                                            'url'   => xarModURL('workflow','user','activities',
                                                                 array('filter_process' => $process['pId']))
                                           );
    }
    return $itemtypes;
}

?>
