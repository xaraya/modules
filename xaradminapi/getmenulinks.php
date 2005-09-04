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
                              'label' => xarML('Monitoring'));
        $menulinks[] = Array('url'   => xarModURL('workflow',
                                                  'admin',
                                                  'processes'),
                              'title' => xarML('Edit the workflow processes'),
                              'label' => xarML('Admin Processes'));
        $menulinks[] = Array('url'   => xarModURL('workflow',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the workflow configuration'),
                              'label' => xarML('Modify Config'));
    }

    return $menulinks;
}

?>
