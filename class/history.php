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
    protected static $objectName = 'workflow_history';
    protected static $fieldList = ['tracker_id', 'workflow', 'user', 'object', 'item', 'transition', 'marking', 'updated', 'context'];

    public static function init(array $args = [])
    {
    }

    // this method overrides the one in xarWorkflowTracker to get the history for trackerId(s)
    public static function getTrackerItems(int|array $trackerIds = [], array $paging = [])
    {
        $filter = [];
        if (!empty($trackerIds)) {
            if (is_array($trackerIds)) {
                $filter[] = implode(",", ["tracker_id", "in", implode(",", $trackerIds)]);
            } else {
                $filter[] = implode(",", ["tracker_id", "eq", (string) $trackerIds]);
            }
        }
        // for paging params see DataObjectLoader = aligned with API params, not DD list params
        $params = ['filter' => $filter];
        if (!empty($paging)) {
            static::setPaging($paging);
        }
        if (!empty(static::$paging)) {
            $params += static::$paging;
        }
        $loader = new DataObjectLoader(static::$objectName, static::$fieldList);
        $items = $loader->query($params);
        // @checkme if we didn't ask for a count in paging, this will return false
        static::$count = $loader->count;
        return array_values($items);
    }

    public static function addItem(int $trackerId, string $workflowName, string $objectName, int $itemId, string $transition, string $marking = '', string $context = '', int $userId = 0)
    {
        if (empty($userId)) {
            $userId = xarSession::getVar('role_id') ?? 0;
        }
        $newItem = [
            'tracker_id' => $trackerId,
            'workflow' => $workflowName,
            'object' => $objectName,
            'item' => $itemId,
            'user' => $userId,
            'transition' => $transition,
            'marking' => $marking,
            'updated' => time(),
            'context' => $context,  // json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];
        $objectRef = DataObjectMaster::getObject(['name' => static::$objectName]);
        $historyId = $objectRef->createItem($newItem);
        xarLog::message("New history item $historyId added");
        return $historyId;
    }
}
