<?php
/**
 * Workflow Module History for Symfony Workflow events
 *
 * This keeps a history of all workflow transitions for all users and object items, and it is never deleted
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

sys::import('modules.workflow.class.tracker');

class xarWorkflowHistory extends xarWorkflowTracker
{
    private static $objectName = 'workflow_history';
    private static $fieldList = ['tracker_id', 'workflow', 'user', 'object', 'item', 'marking', 'transition', 'updated', 'context'];

    public static function init(array $args = [])
    {
    }

    // this method overrides the one in xarWorkflowTracker to get the history for trackerId(s)
    public static function getTrackerItems(int|array $trackerIds = [])
    {
        if (is_array($trackerIds)) {
            // @todo get items for an array of trackerIds
            return [];
        }
        // @todo get items for a particular trackerId
        return [];
    }

    public static function addItem(int $trackerId, string $workflowName, string $objectName, int $itemId, string $marking, string $transition, int $userId = 0, string $context = '')
    {
        if (empty($userId)) {
            $userId = xarSession::getVar('role_id');
        }
        $newItem = [
            'tracker_id' => $trackerId,
            'workflow' => $workflowName,
            'object' => $objectName,
            'item' => $itemId,
            'user' => $userId,
            'marking' => $marking,
            'transition' => $transition,
            'updated' => time(),
            'context' => $context,  // json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];
        $objectRef = DataObjectMaster::getObject(['name' => static::$objectName]);
        $historyId = $objectRef->createItem($newItem);
        echo "New history item $historyId added\n";
        return $historyId;
    }
}
