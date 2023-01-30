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
function workflow_user_test_run(array $args = [])
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

    $invalid = [];
    if (empty($data['workflow'])) {
        $invalid[] = 'workflow';
    }
    if (empty($data['trackerId']) && empty($data['subjectId'])) {
        $invalid[] = 'trackerId or subjectId';
    }
    if (!empty($invalid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = [join(', ', $invalid), 'user', 'test_run', 'workflow'];
        throw new BadParameterException($vars, $msg);
    }

    sys::import('modules.workflow.class.tracker');

    if (!empty($data['trackerId'])) {
        $item = xarWorkflowTracker::getTrackerItem($data['trackerId']);
        if (empty($item)) {
            $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
            $vars = ['trackerId', 'user', 'test_run', 'workflow'];
            throw new BadParameterException($vars, $msg);
        }
        $data['place'] = $item['marking'];
    } elseif (!empty($data['subjectId'])) {
        [$objectName, $itemId] = explode('.', $data['subjectId'] . '.0');
        if (empty($objectName) || empty($itemId)) {
            $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
            $vars = ['subjectId', 'user', 'test_run', 'workflow'];
            throw new BadParameterException($vars, $msg);
        }
        $items = xarWorkflowTracker::getSubjectItems($data['subjectId'], $data['workflow']);
        if (!empty($items)) {
            // @checkme there can be only one :-)
            $item = array_values($items)[0];
            $data['place'] = $item['marking'];
            $data['trackerId'] = $item['id'];
        } else {
            $data['place'] = xarWorkflowConfig::getInitialMarking($data['workflow']);
            $item = ['id' => 0, 'workflow' => $data['workflow'], 'object' => $objectName, 'item' => $itemId, 'user' => xarSession::getVar('role_id'), 'marking' => $data['place'], 'updated' => time()];
        }
    } else {
        //$subject = new xarWorkflowSubject();
        // initiate workflow
        //$marking = $workflow->getMarking($subject);
        throw new Exception('How did we end up here?');
    }

    // <xar:workflow-actions name="actions" config="$config" item="$item" title="$item['marking']" template="$item['marking']"/>
    if (empty($data['transition'])) {
        $data['item'] = $item;
        return $data;
    }

    // @checkme we DO actually need to require composer autoload here
    try {
        //xarWorkflowConfig::checkAutoload();
        xarWorkflowConfig::setAutoload();
    } catch (Exception $e) {
        $data['warning'] = nl2br($e->getMessage());
        return xarTpl::module('workflow', 'user', 'test', $data);
    }

    sys::import('modules.workflow.class.logger');
    sys::import('modules.workflow.class.process');
    sys::import('modules.workflow.class.subject');
    xarWorkflowProcess::setLogger(new xarWorkflowLogger());

    $workflow = xarWorkflowProcess::getProcess($data['workflow']);

    $subject = new xarWorkflowSubject($item['object'], (int) $item['item']);
    if (!empty($data['trackerId'])) {
        // set current marking
        $subject->setMarking($item['marking'], $item);
    } else {
        // initiate workflow for this subject
        $marking = $workflow->getMarking($subject);
        $data['place'] = implode(', ', $marking->getPlaces());
    }

    $transitions = $workflow->getEnabledTransitions($subject);
    // request transition
    if ($workflow->can($subject, $data['transition'])) {
        $marking = $workflow->apply($subject, $data['transition']);
        $data['place'] = implode(', ', array_keys($marking->getPlaces()));
        $data['message'] = "The transition of subject " . $subject->getId() . " to " . xarWorkflowConfig::formatName($data['place']) . " was successful";
    } else {
        $blockers = $workflow->buildTransitionBlockerList($subject, $data['transition']);
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        foreach ($blockers as $blocker) {
            $msg .= "\nBlocker: " . $blocker->getMessage();
        }
        $vars = ['transition', 'user', 'test_run', 'workflow'];
        throw new BadParameterException($vars, $msg);
    }
    unset($data['place']);

    return xarTpl::module('workflow', 'user', 'test', $data);
}
