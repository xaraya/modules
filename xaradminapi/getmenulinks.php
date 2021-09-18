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
function workflow_adminapi_getmenulinks()
{
    $menulinks = [];

    // Security Check
    if (xarSecurity::check('AdminWorkflow', 0)) {
        $menulinks[] = ['url'   => xarController::URL(
            'workflow',
            'admin',
            'monitor_processes'
        ),
                              'title' => xarML('Monitor the workflow processes'),
                              'label' => xarML('Monitoring'), ];
        $menulinks[] = ['url'   => xarController::URL(
            'workflow',
            'admin',
            'processes'
        ),
                              'title' => xarML('Edit the workflow processes'),
                              'label' => xarML('Admin Processes'), ];
        $menulinks[] = ['url'   => xarController::URL(
            'workflow',
            'admin',
            'modifyconfig'
        ),
                              'title' => xarML('Modify the workflow configuration'),
                              'label' => xarML('Modify Config'), ];
    }

    return $menulinks;
}
