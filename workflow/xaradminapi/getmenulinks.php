<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function workflow_adminapi_getmenulinks()
{
    $menulinks = array();

// Security Check
    if (xarSecurityCheck('AdminWorkflow',0)) {
        $menulinks[] = Array('url'   => xarModURL('workflow',
                                                  'admin',
                                                  'monitor_processes'),
                              'title' => xarML('Monitor the workflow processes'),
                              'label' => xarML('Monitor Processes'));
        $menulinks[] = Array('url'   => xarModURL('workflow',
                                                  'admin',
                                                  'monitor_activities'),
                              'title' => xarML('Monitor the workflow activities'),
                              'label' => xarML('Monitor Activities'));
        $menulinks[] = Array('url'   => xarModURL('workflow',
                                                  'admin',
                                                  'monitor_instances'),
                              'title' => xarML('Monitor the workflow instances'),
                              'label' => xarML('Monitor Instances'));
        $menulinks[] = Array('url'   => xarModURL('workflow',
                                                  'admin',
                                                  'monitor_workitems'),
                              'title' => xarML('Monitor the workflow workitems'),
                              'label' => xarML('Monitor Workitems'));
        $menulinks[] = Array('url'   => xarModURL('workflow',
                                                  'admin',
                                                  'processes'),
                              'title' => xarML('Edit the workflow processes'),
                              'label' => xarML('Admin Processes'));
    }

    return $menulinks;
}

?>
