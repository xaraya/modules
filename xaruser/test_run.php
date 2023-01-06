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
 * the test user function - run the actual transition in the workflow here
 *
 * @author mikespub
 * @access public
 * @param no $ parameters
 * @return array empty
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function workflow_user_test_run()
{
    // Security Check
    if (!xarSecurity::check('ReadWorkflow')) {
        return;
    }

    $data = [];
    // @checkme we DO actually need to require composer autoload here
    sys::import('modules.workflow.class.config');
    try {
        //xarWorkflowConfig::checkAutoload();
        xarWorkflowConfig::setAutoload();
    } catch (Exception $e) {
        $data['warning'] = nl2br($e->getMessage());
    }
    sys::import('modules.workflow.class.logger');
    sys::import('modules.workflow.class.process');
    sys::import('modules.workflow.class.subject');
    xarWorkflowProcess::setLogger(new xarWorkflowLogger());

    xarVar::fetch('workflow', 'isset', $data['workflow'], null, xarVar::NOT_REQUIRED);
    xarVar::fetch('trackerId', 'isset', $data['trackerId'], null, xarVar::NOT_REQUIRED);
    xarVar::fetch('subjectId', 'isset', $data['subjectId'], null, xarVar::NOT_REQUIRED);
    xarVar::fetch('place', 'isset', $data['place'], null, xarVar::NOT_REQUIRED);
    xarVar::fetch('transition', 'isset', $data['transition'], null, xarVar::NOT_REQUIRED);

    if (!empty($data['transition'])) {
        $workflow = xarWorkflowProcess::getProcess($data['workflow']);
        if (!empty($data['trackerId'])) {
            $item = xarWorkflowTracker::getTrackerItem($data['trackerId']);
            $subject = new xarWorkflowSubject($item['object'], (int) $item['item']);
            // set current marking
            $subject->setMarking($item['marking'], $item);
        } elseif (!empty($data['subjectId'])) {
            [$objectName, $itemId] = explode('.', $data['subjectId'] . '.0');
            $subject = new xarWorkflowSubject($objectName, (int) $itemId);
            // initiate workflow
            $marking = $workflow->getMarking($subject);
        } else {
            $subject = new xarWorkflowSubject();
            // initiate workflow
            $marking = $workflow->getMarking($subject);
        }
        //$marking = $workflow->getMarking($subject);
        $transitions = $workflow->getEnabledTransitions($subject);
        // request transition
        if ($workflow->can($subject, $data['transition'])) {
            $marking = $workflow->apply($subject, $data['transition']);
        }
    }
    return xarTpl::module('workflow', 'user', 'test', $data);
}
