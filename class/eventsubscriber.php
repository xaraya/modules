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
    public function logEvent(string $eventName, Event|GuardEvent $event)
    {
        // @todo tie in with Xaraya event/hook system for some events
        echo sprintf(
            'Subject (id: "%s") had event "%s" for transition "%s" from "%s" to "%s"' . "\n",
            $event->getSubject()->getId(),
            $eventName,
            $event->getTransition()->getName(),
            implode(', ', array_keys($event->getMarking()->getPlaces())),
            implode(', ', $event->getTransition()->getTos())
        );
    }

    public function onGuardEvent(GuardEvent $event)
    {
        $subject = $event->getSubject();
        $this->logEvent('onGuardEvent', $event);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.guard' => ['onGuardEvent'],
            ];
    }
}
