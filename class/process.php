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

sys::import('modules.workflow.class.eventsubscriber');
sys::import('modules.workflow.class.tracker');

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

class xarWorkflowProcess extends xarObject
{
    public static $config = [];
    public static $workflows = [];
    public static $dispatcher;

    public static function init(array $args = [])
    {
        static::loadConfig();
    }

    public static function loadConfig()
    {
        if (!empty(static::$config)) {
            return static::$config;
        }
        static::$config = [];
        //$configFile = sys::varpath() . '/cache/processes/config.json';
        $configFile = dirname(__DIR__).'/xardata/config.workflows.php';
        if (file_exists($configFile)) {
            //$contents = file_get_contents($configFile);
            //static::$config = json_decode($contents, true);
            static::$config = include($configFile);
        }
        return static::$config;
    }

    public static function hasWorkflowConfig(string $workflowName)
    {
        static::loadConfig();
        if (!empty(static::$config) && !empty(static::$config[$workflowName])) {
            return true;
        }
        return false;
    }

    public static function getWorkflowConfig(string $workflowName)
    {
        if (!static::hasWorkflowConfig($workflowName)) {
            throw new Exception('Unknown workflow ' . $workflowName);
        }
        return static::$config[$workflowName];
    }

    public static function getEventDispatcher()
    {
        // See https://symfony.com/doc/current/components/event_dispatcher.html#using-event-subscribers
        if (empty(static::$dispatcher)) {
            static::$dispatcher = new EventDispatcher();
        }
        // @checkme do this *after* adding the subscribed events and callback functions
        //$dispatcher->addSubscriber($subscriber);
        return static::$dispatcher;
    }

    // @checkme add subscribed events for each object supported by this workflow?
    public static function getEventSubscriber(string $workflowName, string $objectName, array|null $eventTypes = null)
    {
        $subscriber = new xarWorkflowEventSubscriber();
        // @checkme this is the list of all possible events we might be interested in
        if (!isset($eventTypes)) {
            $eventTypes = ['guard', 'leave', 'transition', 'enter', 'entered', 'completed', 'announce'];
        }
        $userId = 6;
        $checkSubjectStatus = [
            'request' => 'available',
        ];
        $callbackFuncs = [
            // @todo this would be where we check the actual status of the subject, rather than the places
            'guard' => function (Event $event, string $eventName) use ($checkSubjectStatus) {
                $transitionName = $event->getTransition()->getName();
                $places = $event->getMarking()->getPlaces();
                //if ($transitionName == 'request' && !in_array('available', array_keys($places))) {
                if (!empty($checkSubjectStatus[$transitionName]) && !in_array($checkSubjectStatus[$transitionName], array_keys($places))) {
                    $message = 'Sorry, this subject is not available...';
                    $event->setBlocked(true, $message);
                    echo "Blocked: $message\n";
                }
            },
            // @checkme this is where we add the successful transition to a new marking to the tracker
            'completed' => function (Event $event, string $eventName) use ($objectName, $userId) {
                $workflowName = $event->getWorkflowName();
                $subject = $event->getSubject();
                $trackerId = xarWorkflowTracker::setItem($workflowName, $objectName, $subject->getId(), $subject->getMarking(), $userId);
            },
        ];
        // @checkme actually we're only interested in events where we have a callback function here :-)
        $eventTypes = array_keys($callbackFuncs);
        foreach ($eventTypes as $eventType) {
            $eventName = $subscriber->addSubscribedEvent($eventType, $workflowName);
            if (!empty($callbackFuncs[$eventType])) {
                $subscriber->addCallbackFunction($eventName, $callbackFuncs[$eventType]);
            }
        }
        return $subscriber;
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
            $info = static::getWorkflowConfig($workflowName);
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
        $eventTypes = $info['events_to_dispatch'] ?? null;
        $subscriber = static::getEventSubscriber($workflowName, $objectName, $eventTypes);
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
            $info = static::getWorkflowConfig($workflowName);
        }
        // @checkme add subscribed events for each object supported by this workflow?
        if (is_array($info['supports'])) {
            $objectName = $info['supports'][0];
        } else {
            $objectName = $info['supports'];
        }
        $dispatcher = static::getEventDispatcher();
        $eventTypes = $info['events_to_dispatch'] ?? null;
        $subscriber = static::getEventSubscriber($workflowName, $objectName, $eventTypes);
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
