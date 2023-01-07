<?php
/**
 * Workflow Module Test Script for Symfony Workflow tests
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

$baseDir = dirname(__DIR__);
$baseDir = '/home/mikespub/xaraya-core';
require $baseDir.'/vendor/autoload.php';
// initialize bootstrap
sys::init();
// initialize caching - delay until we need results
xarCache::init();
// initialize database - delay until caching fails
xarDatabase::init();
// initialize modules
//xarMod::init();
// initialize users
//xarUser::init();
sys::import('modules.workflow.class.process');
sys::import('modules.workflow.class.subject');
sys::import('modules.workflow.class.tracker');
//sys::import('modules.workflow.class.logger');

use Symfony\Component\Workflow\Workflow;

//xarWorkflowProcess::setLogger(new xarWorkflowLogger());

$workflow = xarWorkflowProcess::getProcess('cd_loans');

$subject = new xarWorkflowSubject('cdcollection');

// initiate workflow
$marking = $workflow->getMarking($subject);
echo "Marking: " . var_export($marking, true) . "\n";
$transitions = $workflow->getEnabledTransitions($subject);
echo "Transitions: " . var_export($transitions, true) . "\n";
foreach ($transitions as $transition) {
    echo "Transition '" . $transition->getName() . "': from '" . implode("', '", $transition->getFroms()) . "' to '" . implode("', '", $transition->getTos()) . "'\n";
}
try {
    $result = $workflow->can($subject, "request");
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}
echo "Result: " . var_export($result, true) . "\n";
$subjectId = $subject->getId();
echo "SubjectId: " . var_export($subjectId, true) . "\n";
$marking = $workflow->apply($subject, "request", [Workflow::DISABLE_ANNOUNCE_EVENT => true]);
echo "Marking: " . var_export($marking, true) . "\n";
$context = $subject->getContext();
echo "Context: " . var_export($context, true) . "\n";
$transitions = $workflow->getEnabledTransition($subject, "request");
//$transitions = $workflow->buildTransitionBlockerList($subject, "request");
echo "Transitions: " . var_export($transitions, true) . "\n";
$items = xarWorkflowTracker::getItems("cd_loans", "cdcollection", 0, '', 6);
echo "Items: " . var_export($items, true) . "\n";
$todo = [];
foreach ($items as $id => $item) {
    $todo[$item['object']] ??= [];
    $todo[$item['object']][] = ((int) $item['item'] > 20) ? ((int) $item['item'] - 20) : (int) $item['item'];
}
echo "Todo: " . var_export($todo, true) . "\n";
foreach ($todo as $object => $itemids) {
    //$values = xarWorkflowTracker::getObjectValues($object, $itemids, ['status']);
    $values = xarWorkflowTracker::getObjectValues($object, $itemids);
    echo "Values: " . var_export($values, true) . "\n";
}
