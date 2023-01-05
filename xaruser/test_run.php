<?php

sys::import('modules.workflow.class.config');
xarWorkflowConfig::setAutoload();
//...
/**
sys::import('modules.workflow.class.process');
sys::import('modules.workflow.class.subject');
sys::import('modules.workflow.class.tracker');

$workflow = xarWorkflowProcess::getProcess('cd_loans');
$subject = new xarWorkflowSubject();
// initiate workflow
$marking = $workflow->getMarking($subject);
// request transition
if ($workflow->can($subject, "request")) {
    $marking = $workflow->apply($subject, "request");
}
//...
 */
