<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */
/**
 * get a pubsub event id, or create the event if necessary
 *
 * @param $args['modid'] the module id for the event
 * @param $args['itemtype'] the itemtype for the event
 * @param $args['cid'] the category id for the event
 * @param $args['extra'] some extra group criteria
 * @param $args['groupdescr'] the group description for the event (currently unused)
 * @returns int
 * @return event ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_checkevent($args)
{
    // Get arguments from argument array
    extract($args);

    if (empty($modid) || !is_numeric($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'module', 'admin', 'checkevent', 'Pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (empty($itemtype) || !is_numeric($itemtype)) {
        $itemtype = 0;
    }
    if (!isset($cid) || !is_numeric($cid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'category', 'admin', 'checkevent', 'Pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubeventstable = $xartable['pubsub_events'];

    // check this event isn't already in the DB
    $query = "SELECT xar_eventid
              FROM  $pubsubeventstable
              WHERE xar_modid = ?
              AND   xar_itemtype = ?
              AND   xar_cid = ?";
    $bindvars = array((int)$modid, (int)$itemtype, (int)$cid);
    if (isset($extra)) {
        $query .= ' AND xar_extra = ?';
        array_push($bindvars, $extra);
    }
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // if event already exists then just return the event id;
    if (!$result->EOF) {
        list($eventid) = $result->fields;
        return $eventid;
    }

    if (!isset($extra)) {
        $extra = '';
    }

    if (empty($groupdescr) || !is_string($groupdescr)) {
        $groupdescr = xarML('Created by checkevent');
    }

    // Get next ID in table
    $eventid = $dbconn->GenId($pubsubeventstable);

    // Add item to events table
    $query = "INSERT INTO $pubsubeventstable (
              xar_eventid,
              xar_modid,
              xar_itemtype,
              xar_cid,
              xar_extra,
              xar_groupdescr)
            VALUES (?,?,?,?,?,?)";

    $bindvars = array((int)$eventid, (int)$modid, (int)$itemtype, (int)$cid, $extra, $groupdescr);
    $result = $dbconn->Execute($query, $bindvars);

    if (!$result) return;

    // Get the ID of the item that was inserted
    $eventid = $dbconn->PO_Insert_ID($pubsubeventstable, 'xar_eventid');

    // return eventID
    return $eventid;

}

?>
