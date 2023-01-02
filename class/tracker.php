<?php
/**
 * Workflow Module Tracker for Symfony Workflow events
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

sys::import('modules.dynamicdata.class.objects.master');
sys::import('modules.dynamicdata.class.objects.loader');

class xarWorkflowTracker extends xarObject
{
    private static $objectName = 'workflow_tracker';
    private static $fieldList = ['workflow', 'user', 'object', 'item', 'marking', 'updated'];
    private static $objectList;
    private static $objectRef;

    public static function init(array $args = [])
    {
    }

    public static function getItems(string $workflowName = '', string $objectName = '', int $itemId = 0, string $marking = '', int $userId = 0)
    {
        if (empty($userId)) {
            $userId = xarSession::getVar('role_id');
        }
        //$objectList = DataObjectMaster::getObjectList(['name' => static::$objectName]);
        //$items = $objectList->getItems(['where' => "user eq $userId"]);
        $filter = [];
        if (!empty($workflowName)) {
            $filter[] = implode(",", ["workflow", "eq", $workflowName]);
        }
        if (!empty($objectName)) {
            $filter[] = implode(",", ["object", "eq", $objectName]);
        }
        if (!empty($itemId)) {
            $filter[] = implode(",", ["item", "eq", (string) $itemId]);
        }
        if (!empty($userId)) {
            $filter[] = implode(",", ["user", "eq", (string) $userId]);
        }
        if (!empty($marking)) {
            $filter[] = implode(",", ["marking", "eq", $marking]);
        }
        $loader = new DataObjectLoader(static::$objectName, static::$fieldList);
        $items = $loader->query(['filter' => $filter]);
        return $items;
    }

    public static function addItem(string $workflowName, string $objectName, int $itemId, string $marking, int $userId = 0)
    {
        if (empty($userId)) {
            $userId = xarSession::getVar('role_id');
        }
        $newItem = [
            'workflow' => $workflowName,
            'object' => $objectName,
            'item' => $itemId,
            'user' => $userId,
            'marking' => $marking,
            'updated' => time(),
        ];
        $oldItems = static::getItems($workflowName, $objectName, $itemId, '', $userId);
        $objectRef = DataObjectMaster::getObject(['name' => static::$objectName]);
        if (empty($oldItems)) {
            $objectRef = DataObjectMaster::getObject(['name' => static::$objectName]);
            $trackerId = $objectRef->createItem($newItem);
            echo "New item $trackerId created\n";
        } elseif (count($oldItems) < 2) {
            $oldItem = array_values($oldItems)[0];
            $objectRef = DataObjectMaster::getObject(['name' => static::$objectName, 'itemid' => $oldItem['id']]);
            $trackerId = $objectRef->updateItem($newItem);
            echo "Old item $trackerId updated\n";
        } else {
            throw new Exception("More than 1 item matches the selection criteria:\n" . var_export($oldItems, true));
        }
        return $trackerId;
    }
}
