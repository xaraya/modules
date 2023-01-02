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

require dirname(__DIR__).'/vendor/autoload.php';

use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Dumper\GraphvizDumper;
use Symfony\Component\Workflow\Dumper\StateMachineGraphvizDumper;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\StateMachine;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Validator\StateMachineValidator;
use Symfony\Component\Workflow\Validator\WorkflowValidator;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\EventDispatcher\EventDispatcher;

// load workflow config
$config = include(dirname(__DIR__).'/xardata/config.workflows.php');

// See https://symfony.com/doc/current/components/event_dispatcher.html#using-event-subscribers
$dispatcher = new EventDispatcher();
$subscriber = new xarWorkflowEventSubscriber();
//$eventTypes = ['guard', 'leave', 'transition', 'enter', 'entered', 'completed', 'announce'];
$eventTypes = ['guard', 'leave', 'transition', 'enter', 'entered', 'completed', 'announce'];
foreach ($eventTypes as $eventType) {
    $subscriber->addSubscribedEvent($eventType);
}
$dispatcher->addSubscriber($subscriber);

foreach ($config as $name => $info) {
    $transitions = [];
    // See https://github.com/symfony/symfony/blob/6.3/src/Symfony/Bundle/FrameworkBundle/DependencyInjection/FrameworkExtension.php#L917
    foreach ($info['transitions'] as $name => $fromto) {
        // @checkme this seems to mean from ALL by default for workflow instead of from ANY!?
        //$transitions[] = new Transition($name, $fromto['from'], $fromto['to']);
        if (is_array($fromto['from']) && count($fromto['from']) > 1) {
            foreach ($fromto['from'] as $from) {
                $transitions[] = new Transition($name, $from, $fromto['to']);
            }
        // @checkme not supported for state_machine, pick the first
        } elseif ($info['type'] == 'state_machine' && is_array($fromto['to']) && count($fromto['to']) > 1) {
            $transitions[] = new Transition($name, $fromto['from'], $fromto['to'][0]);
        } else {
            $transitions[] = new Transition($name, $fromto['from'], $fromto['to']);
        }
    }

    $definition = new Definition($info['places'], $transitions, $info['initial_marking']);

    if ($info['type'] == 'workflow') {
        // See $info['marking_store'] for customisation per workflow
        $markingStore = new MethodMarkingStore();

        $workflow = new Workflow($definition, $markingStore, $dispatcher, $name, $info['events_to_dispatch'] ?? null);

        // Throws InvalidDefinitionException in case of an invalid definition
        $validator = new WorkflowValidator();
        $validator->validate($definition, $name);

    // @checkme this creates the wrong graph if we split the from ANY above - it's better with ALL
    // php test.php | dot -Tpng -o cd_loans.png
    //$dumper = new GraphvizDumper();
    //echo $dumper->dump($definition);
    //continue;
    } else {
        // See $info['marking_store'] for customisation per workflow
        $markingStore = new MethodMarkingStore(true);

        $workflow = new StateMachine($definition, $markingStore, $dispatcher, $name, $info['events_to_dispatch'] ?? null);

        // Throws InvalidDefinitionException in case of an invalid definition
        $validator = new StateMachineValidator();
        $validator->validate($definition, $name);

        // @checkme this creates the wrong graph if we split the from ANY above - it's better with ALL
        // php test.php | dot -Tpng -o cd_loans.png
        $dumper = new StateMachineGraphvizDumper();
        echo $dumper->dump($definition);
        continue;
    }

    $subject = new xarWorkflowSubject();
    // initiate workflow
    $marking = $workflow->getMarking($subject);
    echo "Marking: " . var_export($marking, true) . "\n";
    $result = $workflow->can($subject, "request");
    echo "Result: " . var_export($result, true) . "\n";
    $marking = $workflow->apply($subject, "request", [Workflow::DISABLE_ANNOUNCE_EVENT => true]);
    echo "Marking: " . var_export($marking, true) . "\n";
    $context = $subject->getContext();
    echo "Context: " . var_export($context, true) . "\n";
    $transitions = $workflow->getEnabledTransition($subject, "request");
    //$transitions = $workflow->buildTransitionBlockerList($subject, "request");
    echo "Transitions: " . var_export($transitions, true) . "\n";
}
