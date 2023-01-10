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
 * the run transition user API function
 *
 * @author mikespub
 * @access public
 */
function workflow_userapi_run_transition($args)
{
    // Security Check
    if (!xarSecurity::check('ReadWorkflow')) {
        return;
    }
    $workflowName = $args['workflow'];
    $subjectId = $args['subjectId'] ?? null;
    $transitionName = $args['transition'];

    sys::import('modules.workflow.class.config');
    if (!xarWorkflowConfig::hasWorkflowConfig($workflowName)) {
        xarLog::message("No workflow found for '$transitionName' in '$workflowName'", xarLog::LEVEL_INFO);
        return false;
    }

    // if we come from a hook function
    if (empty($subjectId)) {
        sys::import('modules.dynamicdata.class.objects.descriptor');
        $moduleName = $args['module'] ?? xarMod::getName();
        $itemType = $args['itemtype'] ?? 0;
        $itemId = $args['itemid'] ?? 0;
        $moduleId = $args['module_id'] ?? xarMod::getRegID($moduleName);
        $info = DataObjectDescriptor::getObjectID(['moduleid' => $moduleId, 'itemtype' => $itemType]);
        if (empty($info) || empty($info['name'])) {
            xarLog::message("No object associated with module '$moduleName' ($moduleId) itemtype '$itemType' for '$transitionName' in '$workflowName'", xarLog::LEVEL_INFO);
            // @checkme create fake objectName for module:itemtype if no object is available for now?
            //return false;
            $objectName = "$moduleName:$itemType";
        } else {
            $objectName = $info['name'];
        }
        $subjectId = implode('.', [$objectName, $itemId]);
    } else {
        [$objectName, $itemId] = explode('.', $subjectId . '.0');
    }
    xarLog::message("We will trigger '$transitionName' for '$subjectId' in '$workflowName' here...", xarLog::LEVEL_INFO);

    // @checkme we DO actually need to require composer autoload here
    xarWorkflowConfig::setAutoload();

    sys::import('modules.workflow.class.logger');
    sys::import('modules.workflow.class.process');
    sys::import('modules.workflow.class.subject');
    xarWorkflowProcess::setLogger(new xarWorkflowLogger());

    $workflow = xarWorkflowProcess::getProcess($workflowName);
    $subject = new xarWorkflowSubject($objectName, (int) $itemId);
    // @checkme since we don't verify the state of the original object here, this will be triggered for
    // each hook event even if it has already been hooked before. So we will get the same trackerId as
    // before (= same workflow, subject and user), but different entries in the history table...
    // initiate workflow for this subject
    $marking = $workflow->getMarking($subject);

    //$transitions = $workflow->getEnabledTransitions($subject);
    // request transition
    if ($workflow->can($subject, $transitionName)) {
        $context = $args;
        $marking = $workflow->apply($subject, $transitionName, $context);
    //$place = implode(', ', array_keys($marking->getPlaces()));
    } else {
        $blockers = $workflow->buildTransitionBlockerList($subject, $transitionName);
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        foreach ($blockers as $blocker) {
            $msg .= "\nBlocker: " . $blocker->getMessage();
        }
        $vars = ['transition', 'user', 'run_transition', 'workflow'];
        throw new BadParameterException($vars, $msg);
    }

    return true;
}
