<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * process a pubsub event, by adding a job for each subscription to the process queue
 * @param $args['modid'] the module id for the event
 * @param $args['itemtype'] the itemtype for the event
 * @param $args['cid'] the category id for the event
 * @param $args['extra'] some extra group criteria, and
 * @param $args['objectid'] the specific object in the module
 * @param $args['id'] the template id for the jobs
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_processevent($args)
{
    // Get arguments from argument array
    extract($args);
    /*
    $invalid = array();
    if (empty($module_id) || !is_numeric($module_id)) {
        $invalid[] = 'module_id';
    }
    if (!isset($cid) || !is_numeric($cid)) {
        $invalid[] = 'cid';
    }
    if (!isset($object_id) || !is_numeric($object_id)) {
        $invalid[] = 'object_id';
    }
    if (!isset($itemid) || !is_numeric($itemid)) {
        $invalid[] = 'itemid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'processevent', 'Pubsub');
        throw new Exception($msg);
    }

    sys::import('modules.dynamicdata.class.properties.master');
    $queue = DataObjectMaster::getObject(array('name' => 'pubsub_process'));
*/
    $tables = xarDB::getTables();
    $q = new Query('SELECT', $tables['pubsub_events']);
    $q->addfield('id');
    if (!empty($object_id)) {
        $q->eq('object_id', $object_id);
    } else {
        $q->eq('module_id', $module_id);
        $q->eq('itemtype', $itemtype);
    }
    $q->eq('event_type', $event_type);
    $q->run();
    $marked_events = $q->output();

    unset($args['cid']);
    if (!empty($object_id)) {
        $args['module_id'] = 0;
        $args['itemtype'] = 0;
    } else {
        $args['object_id'] = 0;
    }
    
    $q = new Query('INSERT', $tables['pubsub_process']);
    foreach ($marked_events as $event) {
        $q->addfield('event_id', $event['id']);
        $q->addfield('object_id', $object_id);
        $q->addfield('module_id', $module_id);
        $q->addfield('itemtype', $itemtype);
        $q->addfield('itemid', $itemid);
        $q->addfield('url', $url);
        $q->addfield('template_id', $template_id);
        $q->addfield('time_created', time());
        $q->addfield('time_modified', time());
        $q->addfield('author', xarUser::getVar('id'));
        $q->addfield('state', $state);
//        $q->qecho();
        $q->run();
        $q->fields = array();
    }
    return true;

    // Create an array to list the subscriptions that need to be processed.
    $markSubscriptions = array();
    $includechildren = xarModVars::get('pubsub', 'includechildren');
    if (!empty($cid) && $includechildren == 1) {
        $ancestors = xarMod::apiFunc('categories', 'user', 'getancestors', array('cid'=>$cid,'order'=>'self'));
        $ancestors = array_keys($ancestors);

        $query = "SELECT pubsubid, cid
                    FROM $pubsubeventstable, $pubsubsubscriptionstable
                   WHERE $pubsubeventstable.eventid = $pubsubsubscriptionstable.eventid
                     AND $pubsubeventstable.modid =?
                     AND $pubsubeventstable.itemtype = ?";
//                      . "
//                     AND $pubsubeventstable. = " . ($cid);
        $bindvars = array((int)$modid, (int)$itemtype);
        if (isset($extra)) {
            $query .= " AND $pubsubeventstable.extra = ?";
            array_push($bindvars, $extra);
        }
        $result = $dbconn->Execute($query, $bindvars);
        if (!$result) {
            return;
        }

        for (; !$result->EOF; $result->MoveNext()) {
            list($pubsubid, $cid) = $result->fields;

            if ($cid == $cid || in_array($cid, $ancestors)) {
                $markSubscriptions[] = $pubsubid;
            }
        }
    } else {
        $query = "SELECT pubsubid
                    FROM $pubsubeventstable, $pubsubsubscriptionstable
                   WHERE $pubsubeventstable.eventid = $pubsubsubscriptionstable.eventid
                     AND $pubsubeventstable.modid = ?
                     AND $pubsubeventstable.itemtype = ?
                     AND $pubsubeventstable.cid = ?";
        $bindvars = array((int)$modid, (int)$itemtype, (int)$cid);
        if (isset($extra)) {
            $query .= " AND $pubsubeventstable.extra = ?";
            array_push($bindvars, $extra);
        }
        $result = $dbconn->Execute($query, $bindvars);
        if (!$result) {
            return;
        }

        for (; !$result->EOF; $result->MoveNext()) {
            list($pubsubid) = $result->fields;

            $markSubscriptions[] = $pubsubid;
        }
    }

    foreach ($markSubscriptions as $pubsubid) {
        // Get next ID in table
        $nextId = $dbconn->GenId($pubsubprocesstable);

        // Add item
        $query = "INSERT INTO $pubsubprocesstable (
                  id,
                  pubsubid,
                  objectid,
                  template_id,
              status)
                VALUES (?,?,?,?,
                  'pending')";
        $bindvars = array((int)$nextId, (int)$pubsubid, (int)$objectid, (int)$template_id);
        $result2 = $dbconn->Execute($query, $bindvars);
        if (!$result2) {
            return;
        }
    }

    return true;
} // END processevent
