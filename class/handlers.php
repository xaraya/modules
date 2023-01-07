<?php
/**
 * Workflow Module Callback Handlers for Symfony Workflow events
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

class xarWorkflowHandlers extends xarObject
{
    // this is where we add the successful transition to a new marking to the tracker
    public static function setTrackerItemHandler()
    {
        sys::import('modules.workflow.class.tracker');
        $handler = function ($event, $eventName) {
            $workflowName = $event->getWorkflowName();
            $subject = $event->getSubject();
            // @checkme assuming subjectId = objectName.itemId here
            [$objectName, $itemId] = explode('.', (string) $subject->getId() . '.0');
            $trackerId = xarWorkflowTracker::setItem($workflowName, $objectName, (int) $itemId, $subject->getMarking());
        };
        return $handler;
    }

    // here you can specify callback functions as transition blockers - expression language is not supported
    // this would be where we check the actual status of the subject, rather than the places
    public static function guardPropertyHandler(array $propertyMapping, array $valueMapping = [])
    {
        sys::import('modules.dynamicdata.class.objects.master');
        $handler = function ($event, $eventName) use ($propertyMapping, $valueMapping) {
            $transitionName = $event->getTransition()->getName();
            $places = $event->getMarking()->getPlaces();
            $subject = $event->getSubject();
            $subjectId = $subject->getId();
            // @checkme assuming subjectId = objectName.itemId here
            [$objectName, $itemId] = explode('.', (string) $subject->getId() . '.0');
            if (!array_key_exists($objectName, $propertyMapping)) {
                $message = "Unexpected subject $subjectId";
                xarLog::message("Event $eventName blocked: $message", xarLog::LEVEL_INFO);
                throw new Exception($message);
            }
            $objectRef = DataObjectMaster::getObject(['name' => $objectName, 'itemid' => $itemId]);
            $id = $objectRef->getItem();
            foreach ($propertyMapping[$objectName] as $propertyName => $match) {
                if (!array_key_exists($propertyName, $objectRef->properties)) {
                    $message = "Sorry, this subject does not have property '$propertyName'";
                    $event->setBlocked(true, $message);
                    xarLog::message("Transition $transitionName blocked: $message", xarLog::LEVEL_INFO);
                }
                $value = $objectRef->properties[$propertyName]->value;
                if ($value != $match) {
                    $message = "Sorry, this subject has '$propertyName' = '$value' instead of '$match'";
                    $event->setBlocked(true, $message);
                    xarLog::message("Transition $transitionName blocked: $message", xarLog::LEVEL_INFO);
                }
            }
        };
        return $handler;
    }

    // here you can specify callback functions to update the actual objects once the transition is completed
    // this would be where we update the actual status of the subject, rather than the places
    public static function updatePropertyHandler(array $propertyMapping, array $valueMapping = [])
    {
        sys::import('modules.dynamicdata.class.objects.master');
        $handler = function ($event, $eventName) use ($propertyMapping, $valueMapping) {
            $workflowName = $event->getWorkflowName();
            $subject = $event->getSubject();
            $transition = $event->getTransition();
            $marking = $event->getMarking();
            //$metadata = $event->getMetadata();
            $subjectId = $subject->getId();
            // @checkme assuming subjectId = objectName.itemId here
            [$objectName, $itemId] = explode('.', (string) $subject->getId() . '.0');
            if (!array_key_exists($objectName, $propertyMapping)) {
                $message = "Unexpected subject $subjectId";
                xarLog::message("Event $eventName stopped: $message", xarLog::LEVEL_INFO);
                throw new Exception($message);
            }
            $newItem = [];
            foreach ($propertyMapping[$objectName] as $propertyName => $value) {
                $newItem[$propertyName] = $value;
            }
            $objectRef = DataObjectMaster::getObject(['name' => $objectName, 'itemid' => $itemId]);
            $id = $objectRef->updateItem($newItem);
        };
        return $handler;
    }
}
