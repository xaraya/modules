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
    public static function setTrackerItem(array $deleteTracker = [])
    {
        sys::import('modules.workflow.class.tracker');
        sys::import('modules.workflow.class.history');
        $handler = function ($event, $eventName) use ($deleteTracker) {
            $workflowName = $event->getWorkflowName();
            $subject = $event->getSubject();
            // @checkme assuming subjectId = objectName.itemId here
            [$objectName, $itemId] = explode('.', (string) $subject->getId() . '.0');
            $transitionName = $event->getTransition()->getName();
            // @checkme delete tracker at the end of this transition - pass along eventName to completed
            $deleteEventName = "workflow.$workflowName.delete.$transitionName";
            if (!empty($deleteTracker) && !empty($deleteTracker[$deleteEventName])) {
                $trackerId = xarWorkflowTracker::deleteItem($workflowName, $objectName, (int) $itemId, $subject->getMarking());
            } else {
                $trackerId = xarWorkflowTracker::setItem($workflowName, $objectName, (int) $itemId, $subject->getMarking());
            }
            $marking = implode(',', array_keys($event->getMarking()->getPlaces()));
            $context = json_encode($event->getContext(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $historyId = xarWorkflowHistory::addItem((int) $trackerId, $workflowName, $objectName, (int) $itemId, $transitionName, $marking, (string) $context);
        };
        return $handler;
    }

    // here you can specify callback functions as transition blockers - expression language is not supported
    public static function guardCheckAdmin($admin, $roleId = null)
    {
        if (empty($admin)) {
            return;
        }
        // @checkme list of admin roles is pre-defined based on default role groups here
        $adminGroups = ['administrators', 'sitemanagers'];
        return static::guardCheckRoles($adminGroups, $roleId);
    }

    // with the corresponding actual function to check yourself e.g. in code or templates
    public static function doCheckAdmin($admin, $roleId = null)
    {
        if (empty($admin)) {
            return true;
        }
        $adminGroups = ['administrators', 'sitemanagers'];
        return static::doCheckRoles($adminGroups, $roleId);
    }

    public static function guardCheckRoles($groupUserNames, $roleId = null)
    {
        sys::import('modules.roles.class.roles');
        $parentRoleIds = static::getGroupRoleIds($groupUserNames);
        // @checkme we only look up the direct parents here
        $handler = function ($event, $eventName) use ($parentRoleIds, $roleId) {
            $userId = $roleId ?? xarSession::getVar('role_id') ?? 0;
            $parents = xarCache::getParents($userId);
            $intersect = array_intersect($parents, $parentRoleIds);
            if (empty($intersect)) {
                $transitionName = $event->getTransition()->getName();
                $message = "Sorry, you do not have the right roles";
                $event->setBlocked(true, $message);
                xarLog::message("Transition $transitionName blocked: $message", xarLog::LEVEL_INFO);
            }
        };
        return $handler;
    }

    public static function doCheckRoles($groupUserNames, $roleId = null)
    {
        $parentRoleIds = static::getGroupRoleIds($groupUserNames);
        $userId = $roleId ?? xarSession::getVar('role_id') ?? 0;
        $parents = xarCache::getParents($userId);
        $intersect = array_intersect($parents, $parentRoleIds);
        return !empty($intersect);
    }

    public static function getGroupRoleIds($groupUserNames)
    {
        $groupRoleIds = [];
        foreach ($groupUserNames as $uname) {
            $role = xarRoles::ufindRole($uname);
            if (empty($role)) {
                xarLog::message("Unknown role '$uname' to check in workflow transition", xarLog::LEVEL_WARNING);
                continue;
            }
            $groupRoleIds[] = $role->getID();
        }
        return $groupRoleIds;
    }

    public static function guardCheckAccess($action, $roleId = null)
    {
        sys::import('modules.dynamicdata.class.objects.master');
        $handler = function ($event, $eventName) use ($action, $roleId) {
            $subjectId = $event->getSubject()->getId();
            // @checkme assuming subjectId = objectName.itemId here
            [$objectName, $itemId] = explode('.', (string) $subjectId . '.0');
            $objectRef = DataObjectMaster::getObject(['name' => $objectName, 'itemid' => $itemId]);
            $userId = $roleId ?? xarSession::getVar('role_id') ?? 0;
            if (empty($objectref) || !$objectRef->checkAccess($action, $itemId, $userId)) {
                $transitionName = $event->getTransition()->getName();
                $message = "Sorry, you do not have '$action' access to this subject";
                $event->setBlocked(true, $message);
                xarLog::message("Transition $transitionName blocked: $message", xarLog::LEVEL_INFO);
            }
        };
        return $handler;
    }

    public static function doCheckAccess($objectName, $itemId, $action, $roleId = null)
    {
        $objectRef = DataObjectMaster::getObject(['name' => $objectName, 'itemid' => $itemId]);
        if (empty($objectRef)) {
            return false;
        }
        $userId = $roleId ?? xarSession::getVar('role_id') ?? 0;
        return $objectRef->checkAccess($action, $itemId, $userId);
    }

    public static function guardSecurityCheck($mask, $catch=0, $component='', $instance='', $module='', $rolename='', $realm=0, $level=0)
    {
        sys::import('modules.privileges.class.security');
        // Fallback for checkAccess:
        // return xarSecurity::check($mask,0,'Item',$this->moduleid.':'.$this->itemtype.':'.$itemid,'',$rolename);
    }

    public static function doSecurityCheck()
    {
        throw new Exception("Use xarSecurity::check() yourself :-)");
    }

    // this would be where we check the actual status of the subject, rather than the places
    public static function guardPropertyHandler(array $propertyMapping, array $valueMapping = [])
    {
        sys::import('modules.dynamicdata.class.objects.master');
        $handler = function ($event, $eventName) use ($propertyMapping, $valueMapping) {
            $transitionName = $event->getTransition()->getName();
            //$places = $event->getMarking()->getPlaces();
            //$subject = $event->getSubject();
            $subjectId = $event->getSubject()->getId();
            // @checkme assuming subjectId = objectName.itemId here
            [$objectName, $itemId] = explode('.', (string) $subjectId . '.0');
            if (!array_key_exists($objectName, $propertyMapping)) {
                $message = "Unexpected subject $subjectId";
                xarLog::message("Event $eventName stopped: $message", xarLog::LEVEL_WARNING);
                throw new Exception($message);
            }
            $objectRef = DataObjectMaster::getObject(['name' => $objectName, 'itemid' => $itemId]);
            if (empty($objectRef)) {
                $message = "Unknown subject $subjectId";
                xarLog::message("Event $eventName stopped: $message", xarLog::LEVEL_WARNING);
                throw new Exception($message);
            }
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

    public static function doCheckProperty($objectName, $itemId, $propertyMapping)
    {
        if (!array_key_exists($objectName, $propertyMapping)) {
            return false;
        }
        $objectRef = DataObjectMaster::getObject(['name' => $objectName, 'itemid' => $itemId]);
        if (empty($objectRef)) {
            return false;
        }
        $id = $objectRef->getItem();
        foreach ($propertyMapping[$objectName] as $propertyName => $match) {
            if (!array_key_exists($propertyName, $objectRef->properties)) {
                return false;
            }
            $value = $objectRef->properties[$propertyName]->value;
            if ($value != $match) {
                return false;
            }
        }
        return true;
    }

    // here you can specify callback functions to update the actual objects once the transition is completed
    // this would be where we update the actual status of the object, rather than the places of the subject
    public static function updatePropertyHandler(array $propertyMapping, array $valueMapping = [])
    {
        sys::import('modules.dynamicdata.class.objects.master');
        $handler = function ($event, $eventName) use ($propertyMapping, $valueMapping) {
            //$workflowName = $event->getWorkflowName();
            //$subject = $event->getSubject();
            //$transition = $event->getTransition();
            //$marking = $event->getMarking();
            //$metadata = $event->getMetadata();
            $subjectId = $event->getSubject()->getId();
            // @checkme assuming subjectId = objectName.itemId here
            [$objectName, $itemId] = explode('.', (string) $subjectId . '.0');
            if (!array_key_exists($objectName, $propertyMapping)) {
                $message = "Unexpected subject $subjectId";
                xarLog::message("Event $eventName stopped: $message", xarLog::LEVEL_WARNING);
                throw new Exception($message);
            }
            $newItem = [];
            foreach ($propertyMapping[$objectName] as $propertyName => $value) {
                $newItem[$propertyName] = $value;
            }
            $objectRef = DataObjectMaster::getObject(['name' => $objectName, 'itemid' => $itemId]);
            if (empty($objectRef)) {
                $message = "Unknown subject $subjectId";
                xarLog::message("Event $eventName stopped: $message", xarLog::LEVEL_WARNING);
                throw new Exception($message);
            }
            $id = $objectRef->updateItem($newItem);
        };
        return $handler;
    }
}
