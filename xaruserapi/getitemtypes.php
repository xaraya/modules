<?php

/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 */
function workflow_userapi_getitemtypes($args)
{
    $itemtypes = array();

// Common setup for Galaxia environment
    include('modules/workflow/tiki-setup.php');
    include(GALAXIA_LIBRARY.'/ProcessMonitor.php');

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
