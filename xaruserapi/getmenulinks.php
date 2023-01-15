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
 * utility function pass individual menu items to the main menu
 *
 * @author the Workflow module development team
 * @return array containing the menulinks for the main menu items.
 */
function workflow_userapi_getmenulinks()
{
    $menulinks = [];

    // Security Check
    if (xarSecurity::check('ReadWorkflow', 0)) {
        $menulinks[] = ['url'   => xarController::URL(
            'workflow',
            'user',
            'display'
        ),
                              'title' => xarML('Links to all the available interactive processes'),
                              'label' => xarML('Runnable Activities'), ];
        $menulinks[] = ['url'   => xarController::URL(
            'workflow',
            'user',
            'processes'
        ),
                              'title' => xarML('View your workflow processes'),
                              'label' => xarML('Processes'), ];
        $menulinks[] = ['url'   => xarController::URL(
            'workflow',
            'user',
            'activities'
        ),
                              'title' => xarML('View your workflow activities'),
                              'label' => xarML('Activities'), ];
        $menulinks[] = ['url'   => xarController::URL(
            'workflow',
            'user',
            'instances'
        ),
                              'title' => xarML('View your workflow instances'),
                              'label' => xarML('Instances'), ];
        $menulinks[] = ['url'   => xarController::URL(
            'workflow',
            'user',
            'test'
        ),
                              'title' => xarML('View your workflow test'),
                              'label' => xarML('Test New Workflows'), ];
    }

    return $menulinks;
}
