<?php
/**
 * Workflow Module Process Creation for Symfony Workflow events
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

sys::import('modules.workflow.class.config');
sys::import('modules.workflow.class.eventsubscriber');
sys::import('modules.workflow.class.logger');

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
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\EventListener\AuditTrailListener;

class xarWorkflowProcess extends xarObject
{
    public static $workflows = [];
    public static $dispatcher;
    public static $logger;

    public static function init(array $args = [])
    {
        xarWorkflowConfig::init($args);
    }

    public static function setLogger($logger)
    {
        static::$logger = $logger;
    }

    public static function getEventDispatcher()
    {
        // See https://symfony.com/doc/current/components/event_dispatcher.html#using-event-subscribers
        if (empty(static::$dispatcher)) {
            static::$dispatcher = new EventDispatcher();
            if (!empty(static::$logger)) {
                static::$dispatcher->addSubscriber(new AuditTrailListener(static::$logger));
            }
        }
        // @checkme do this *after* adding the subscribed events and callback functions
        //$dispatcher->addSubscriber($subscriber);
        return static::$dispatcher;
    }

    // @checkme add subscribed events for each object supported by this workflow?
    public static function getEventSubscriber(string $workflowName, string $objectName, array $callbackList)
    {
        $subscriber = new xarWorkflowEventSubscriber();
        foreach ($callbackList as $transitionName => $callbackFuncs) {
            foreach (array_keys($callbackFuncs) as $eventType) {
                $eventName = $subscriber->addSubscribedEvent($eventType, $workflowName, $transitionName);
                $subscriber->addCallbackFunction($eventName, $callbackFuncs[$eventType]);
            }
        }
        sys::import('modules.workflow.class.handlers');
        // @checkme this is where we add the successful transition to a new marking to the tracker
        $eventType = 'completed';
        $eventName = $subscriber->addSubscribedEvent($eventType);
        $subscriber->addCallbackFunction($eventName, xarWorkflowHandlers::setTrackerItemHandler());
        return $subscriber;
    }

    public static function getCallbackList(array $info)
    {
        $callbackList = [];
        // @checkme this is the list of all possible events we might be interested in
        //$eventTypes = ['guard', 'leave', 'transition', 'enter', 'entered', 'completed', 'announce'];
        foreach ($info['transitions'] as $transitionName => $fromto) {
            if (!empty($fromto['guard'])) {
                $callbackList[$transitionName] ??= [];
                $callbackList[$transitionName]['guard'] = $fromto['guard'];
            }
            if (!empty($fromto['completed'])) {
                $callbackList[$transitionName] ??= [];
                $callbackList[$transitionName]['completed'] = $fromto['completed'];
            }
        }
        return $callbackList;
    }

    public static function getTransitions(array $transitionsConfig, string $workflowType)
    {
        $transitions = [];
        // See https://github.com/symfony/symfony/blob/6.3/src/Symfony/Bundle/FrameworkBundle/DependencyInjection/FrameworkExtension.php#L917
        foreach ($transitionsConfig as $transitionName => $fromto) {
            // @checkme this seems to mean from ALL by default for workflow instead of from ANY!?
            //$transitions[] = new Transition($transitionName, $fromto['from'], $fromto['to']);
            if (is_array($fromto['from']) && count($fromto['from']) > 1) {
                foreach ($fromto['from'] as $from) {
                    $transitions[] = new Transition($transitionName, $from, $fromto['to']);
                }
            // @checkme not supported for state_machine, pick the first
            } elseif ($workflowType == 'state_machine' && is_array($fromto['to']) && count($fromto['to']) > 1) {
                $transitions[] = new Transition($transitionName, $fromto['from'], $fromto['to'][0]);
            } else {
                $transitions[] = new Transition($transitionName, $fromto['from'], $fromto['to']);
            }
        }
        return $transitions;
    }

    public static function getProcess(string $workflowName)
    {
        if (empty(static::$workflows[$workflowName])) {
            static::$workflows[$workflowName] = static::buildWorkflow($workflowName);
        }
        return static::$workflows[$workflowName];
    }

    public static function buildWorkflow(string $workflowName, array $info = [])
    {
        if (empty($info)) {
            $info = xarWorkflowConfig::getWorkflowConfig($workflowName);
        }
        if ($info['type'] == 'state_machine') {
            return static::buildStateMachine($workflowName, $info);
        }
        // @checkme add subscribed events for each object supported by this workflow?
        if (is_array($info['supports'])) {
            $objectName = $info['supports'][0];  // pick the first one for now...
        } else {
            $objectName = $info['supports'];
        }
        $dispatcher = static::getEventDispatcher();
        // @checkme we need at least ['workflow.completed'] + callbackList here
        $eventTypes = $info['events_to_dispatch'] ?? null;
        // @checkme add guard and completed callback functions per transaction
        $callbackList = static::getCallbackList($info);
        $subscriber = static::getEventSubscriber($workflowName, $objectName, $callbackList);
        // @checkme do this *after* adding the subscribed events and callback functions
        $dispatcher->addSubscriber($subscriber);

        $transitions = static::getTransitions($info['transitions'], $info['type']);

        $definition = new Definition($info['places'], $transitions, $info['initial_marking']);

        // See $info['marking_store'] for customisation per workflow - multiple_state here
        $markingStore = new MethodMarkingStore();

        $workflow = new Workflow($definition, $markingStore, $dispatcher, $workflowName, $eventTypes);

        // Throws InvalidDefinitionException in case of an invalid definition
        $validator = new WorkflowValidator();
        $validator->validate($definition, $workflowName);

        return $workflow;
    }

    public static function buildStateMachine(string $workflowName, array $info = [])
    {
        if (empty($info)) {
            $info = xarWorkflowConfig::getWorkflowConfig($workflowName);
        }
        // @checkme add subscribed events for each object supported by this workflow?
        if (is_array($info['supports'])) {
            $objectName = $info['supports'][0];
        } else {
            $objectName = $info['supports'];
        }
        $dispatcher = static::getEventDispatcher();
        // @checkme we need at least ['workflow.completed'] + callbackList here
        $eventTypes = $info['events_to_dispatch'] ?? null;
        // @checkme add guard and completed callback functions per transaction
        $callbackList = static::getCallbackList($info);
        $subscriber = static::getEventSubscriber($workflowName, $objectName, $callbackList);
        // @checkme do this *after* adding the subscribed events and callback functions
        $dispatcher->addSubscriber($subscriber);

        $transitions = static::getTransitions($info['transitions'], $info['type']);

        $definition = new Definition($info['places'], $transitions, $info['initial_marking']);

        // See $info['marking_store'] for customisation per workflow - single_state here
        $markingStore = new MethodMarkingStore(true);

        $workflow = new StateMachine($definition, $markingStore, $dispatcher, $workflowName, $eventTypes);

        // Throws InvalidDefinitionException in case of an invalid definition
        $validator = new StateMachineValidator();
        $validator->validate($definition, $workflowName);

        return $workflow;
    }

    public static function dumpProcess(string $workflowName)
    {
        $workflow = static::getProcess($workflowName);
        if ($workflow instanceof StateMachine) {
            // php test.php | dot -Tpng -o cd_loans.png
            $dumper = new StateMachineGraphvizDumper();
            return $dumper->dump($workflow->getDefinition(), null, ['node' => ['href' => '/'], 'edge' => ['href' => '/']]);
        }
        // @checkme this creates the wrong graph if we split the from ANY above - it's better with ALL
        // php test.php | dot -Tpng -o cd_loans.png
        $dumper = new GraphvizDumper();
        //return $dumper->dump($workflow->getDefinition(), null, ['graph' => ['href' => '/'], 'node' => ['href' => '/']]);
        return $dumper->dump($workflow->getDefinition(), null, ['node' => ['href' => '/']]);
    }

    // See https://github.com/symfony/symfony/blob/6.3/src/Symfony/Component/Workflow/Workflow.php
    public static function canTransition(string $workflowName, object $subject, string $transitionName)
    {
        // @checkme the subject has its own method to check a transition
        if (method_exists($subject, 'canTransition')) {
            return $subject->canTransition($workflowName, $transitionName);
        }
        $workflow = static::getProcess($workflowName);
        return $workflow->can($subject, $transitionName);
    }

    public static function applyTransition(string $workflowName, object $subject, string $transitionName, array $context = [])
    {
        // @checkme the subject has its own method to apply the transition
        if (method_exists($subject, 'applyTransition')) {
            return $subject->applyTransition($workflowName, $transitionName, $context);
        }
        $workflow = static::getProcess($workflowName);
        return $workflow->apply($subject, $transitionName, $context);
    }

    public static function getEnabledTransitions(string $workflowName, object $subject)
    {
        // @checkme the subject has its own method to get enabled transitions
        if (method_exists($subject, 'getEnabledTransitions')) {
            return $subject->getEnabledTransitions($workflowName);
        }
        $workflow = static::getProcess($workflowName);
        return $workflow->getEnabledTransitions($subject);
    }

    // See https://github.com/symfony/symfony/blob/6.3/src/Symfony/Component/Workflow/Registry.php
    public function hasWorkflow(object $subject, string $workflowName)
    {
        return $subject->hasWorkflow($workflowName);
    }

    public function getWorkflow(object $subject, string $workflowName)
    {
        return $subject->getWorkflow($workflowName);
    }

    public function addWorkflow(object $subject, string $workflowName, $workflow = [])
    {
        return $subject->addWorkflow($workflowName, $workflow);
    }

    public function allWorkflows(object $subject)
    {
        return $subject->allWorkflows();
    }

    public function supportsWorkflow(object $subject, string $workflowName)
    {
        return $subject->supportsWorkflow($workflowName);
    }
}
