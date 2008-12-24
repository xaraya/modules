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
