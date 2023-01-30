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
 * the test user function
 *
 * @author mikespub
 * @access public
 * @param no $ parameters
 * @return array empty
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function workflow_user_test(array $args = [])
{
    // Security Check
    if (!xarSecurity::check('ReadWorkflow')) {
        return;
    }

    $data = $args ?? [];
    $data['warning'] = '';
    // @checkme we don't actually need to require composer autoload here
    sys::import('modules.workflow.class.config');
    try {
        xarWorkflowConfig::checkAutoload();
        //xarWorkflowConfig::setAutoload();
    } catch (Exception $e) {
        $data['warning'] = nl2br($e->getMessage());
    }
    $data['config'] = xarWorkflowConfig::loadConfig();

    xarVar::fetch('workflow', 'isset', $data['workflow'], null, xarVar::NOT_REQUIRED);
    xarVar::fetch('trackerId', 'isset', $data['trackerId'], null, xarVar::NOT_REQUIRED);
    xarVar::fetch('subjectId', 'isset', $data['subjectId'], null, xarVar::NOT_REQUIRED);
    xarVar::fetch('place', 'isset', $data['place'], null, xarVar::NOT_REQUIRED);
    xarVar::fetch('transition', 'isset', $data['transition'], null, xarVar::NOT_REQUIRED);
    return $data;
}
