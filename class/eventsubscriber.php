<?php
/**
 * Workflow Module Event Subscriber for Symfony Workflow events
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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;

class xarWorkflowEventSubscriber implements EventSubscriberInterface
{
    private static $subscribedEvents = [];
    private static $eventTypeMethods = [
        'guard' => 'onGuardEvent',
        'leave' => 'onLeaveEvent',
        'transition' => 'onTransitionEvent',
        'enter' => 'onEnterEvent',
        'entered' => 'onEnteredEvent',
        'completed' => 'onCompletedEvent',
        'announce' => 'onAnnounceEvent',
    ];

    public function addSubscribedEvent(string $eventType, string $workflowName = '', string $specificName = '')
    {
        static::$subscribedEvents[] = [$eventType, $workflowName, $specificName];
    }

    public function logEvent(Event|GuardEvent $event, string $eventName)
    {
        // @todo tie in with Xaraya event/hook system for some events
        $marking = $event->getMarking();
        $subject = $event->getSubject();
        $transition = $event->getTransition();
        //$workflowName = $event->getWorkflowName();
        //$metadata = $event->getMetadata();
        echo sprintf(
            'Subject (id: "%s") had event "%s" for transition "%s" from "%s" to "%s"' . "\n",
            isset($subject) ? $subject->getId() : '',
            $eventName,
            isset($transition) ? $transition->getName() : '',
            isset($marking) ? implode(', ', array_keys($marking->getPlaces())) : '',
            isset($transition) ? implode(', ', $transition->getTos()) : ''
        );
    }

    public function onGuardEvent(GuardEvent $event, string $eventName)
    {
        //$subject = $event->getSubject();
        $this->logEvent($event, $eventName);
        $transitionName = $event->getTransition()->getName();
        $places = $event->getMarking()->getPlaces();
        if ($transitionName == 'request' && !in_array('available', array_keys($places))) {
            $message = 'Sorry, this subject is not available...';
            $event->setBlocked(true, $message);
            echo "Blocked: $message\n";
        }
    }

    public function onLeaveEvent(Event $event, string $eventName)
    {
        //$subject = $event->getSubject();
        $this->logEvent($event, $eventName);
    }

    public function onTransitionEvent(Event $event, string $eventName)
    {
        //$subject = $event->getSubject();
        $this->logEvent($event, $eventName);
    }

    public function onEnterEvent(Event $event, string $eventName)
    {
        //$subject = $event->getSubject();
        $this->logEvent($event, $eventName);
    }

    public function onEnteredEvent(Event $event, string $eventName)
    {
        //$subject = $event->getSubject();
        $this->logEvent($event, $eventName);
    }

    public function onCompletedEvent(Event $event, string $eventName)
    {
        //$subject = $event->getSubject();
        $this->logEvent($event, $eventName);
    }

    public function onAnnounceEvent(Event $event, string $eventName)
    {
        //$subject = $event->getSubject();
        $this->logEvent($event, $eventName);
    }

    public static function getSubscribedEvents()
    {
        //return [
        //    'workflow.guard' => ['onGuardEvent'],
        //];
        $mapping = [];
        foreach (static::$subscribedEvents as list($eventType, $workflowName, $specificName)) {
            //workflow.guard
            if (empty($workflowName)) {
                $eventName = 'workflow.' . $eventType;
            //workflow.[workflow name].guard
            } else {
                $eventName = 'workflow.' . $workflowName . '.' . $eventType;
                //workflow.[workflow name].guard.[transition name]
                if (!empty($specificName)) {
                    $eventName .= '.' . $specificName;
                }
            }
            $mapping[$eventName] = [static::$eventTypeMethods[$eventType]];
        }
        return $mapping;
    }
}
