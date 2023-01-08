<?php
/**
 * Workflow Module REST API for Symfony Workflow Component (test)
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
 * Sample REST API call supported by this module (if any)
 *
 * @return array of info
 */
function workflow_restapi_test($args = [])
{
    xarLog::init();
    // @checkme pass all args from handler here?
    //extract($args);
    $result = $args;
    $what = $args['what'] ?? '';
    switch ($what) {
        case 'config':
            sys::import('modules.workflow.class.config');
            $config = xarWorkflowConfig::loadConfig();
            $workflowName = $args['workflow'] ?? '';
            if (empty($workflowName)) {
                return $config;
            }
            if (empty($config[$workflowName])) {
                return "Invalid workflow '$workflowName'";
            }
            $result = $config[$workflowName];
            break;
        case 'process':
            sys::import('modules.workflow.class.process');
            $workflowName = $args['workflow'] ?? '';
            $workflow = xarWorkflowProcess::getProcess($workflowName);
            $result = xarWorkflowProcess::showProcess($workflow);
            break;
        case 'tracker':
            sys::import('modules.workflow.class.tracker');
            $workflowName = $args['workflow'] ?? '';
            $subjectId = $args['subjectId'] ?? '';
            $trackerId = $args['trackerId'] ?? 0;
            $paging = [];
            // @checkme we don't support filter here since we're already filtering in tracker
            //$allowed = array_flip(['order', 'offset', 'limit', 'filter', 'count', 'access']);
            $paging_params = ['order', 'offset', 'limit', 'count'];
            foreach ($paging_params as $param) {
                if (!empty($args[$param])) {
                    $paging[$param] = $args[$param];
                }
            }
            if (!empty($paging)) {
                //$paging['count'] = true;
                xarWorkflowTracker::setPaging($paging);
            }
            if (!empty($trackerId)) {
                $result = xarWorkflowTracker::getTrackerItem($trackerId);
            } elseif (!empty($subjectId)) {
                $result = xarWorkflowTracker::getSubjectItems($subjectId, $workflowName);
            } else {
                $result = xarWorkflowTracker::getWorkflowItems($workflowName);
            }
            //$result['paging'] = $paging;
            //$result['paging']['count'] = xarWorkflowTracker::getCount();
            break;
        case 'history':
            sys::import('modules.workflow.class.history');
            $workflowName = $args['workflow'] ?? '';
            $subjectId = $args['subjectId'] ?? '';
            $trackerId = $args['trackerId'] ?? 0;
            $paging = [];
            // @checkme we don't support filter here since we're already filtering in tracker
            //$allowed = array_flip(['order', 'offset', 'limit', 'filter', 'count', 'access']);
            $paging_params = ['order', 'offset', 'limit', 'count'];
            foreach ($paging_params as $param) {
                if (!empty($args[$param])) {
                    $paging[$param] = $args[$param];
                }
            }
            if (!empty($paging)) {
                //$paging['count'] = true;
                xarWorkflowHistory::setPaging($paging);
            }
            if (!empty($trackerId)) {
                $result = xarWorkflowHistory::getTrackerItems($trackerId);
            } elseif (!empty($subjectId)) {
                $result = xarWorkflowHistory::getSubjectItems($subjectId, $workflowName);
            } else {
                $result = xarWorkflowHistory::getWorkflowItems($workflowName);
            }
            //$result['paging'] = $paging;
            //$result['paging']['count'] = xarWorkflowHistory::getCount();
            break;
        case 'subject':
            sys::import('modules.workflow.class.subject');
            $objectName = $args['object'] ?? '';
            $itemId = $args['item'] ?? 0;
            $subject = new xarWorkflowSubject($objectName, (int) $itemId);
            $result = [
                'marking' => $subject->getMarking(),
                'context' => $subject->getContext(),
                'objectref' => $subject->getObject()->getFieldValues([], 1),
            ];
            break;
        default:
            break;
    }
    //xarVar::fetch('name', 'isset', $name, null, xarVar::NOT_REQUIRED);
    return $result;
}
