<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function workflow_userapi_getmenulinks()
{
    $menulinks = array();

// Security Check
    if (xarSecurityCheck('ReadWorkflow',0)) {
        $menulinks[] = Array('url'   => xarModURL('workflow',
                                                  'user',
                                                  'processes'),
                              'title' => xarML('View your workflow processes'),
                              'label' => xarML('View Processes'));
        $menulinks[] = Array('url'   => xarModURL('workflow',
                                                  'user',
                                                  'activities'),
                              'title' => xarML('View your workflow activities'),
                              'label' => xarML('View Activities'));
        $menulinks[] = Array('url'   => xarModURL('workflow',
                                                  'user',
                                                  'instances'),
                              'title' => xarML('View your workflow instances'),
                              'label' => xarML('View Instances'));
    }

    return $menulinks;
}

?>
