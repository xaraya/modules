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
 * get a pubsub event id, or create the event if necessary
 *
 * @param $args['modid'] the module id for the event
 * @param $args['itemtype'] the itemtype for the event
 * @param $args['cid'] the category id for the event
 * @param $args['extra'] some extra group criteria
 * @param $args['groupdescr'] the group description for the event (currently unused)
 * @returns int
 * @return event ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_checkevent($args)
{
    // Get arguments from argument array
    extract($args);

    if (empty($modid) || !is_numeric($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'module', 'admin', 'checkevent', 'Pubsub');
        throw new Exception($msg);
    }
    if (empty($itemtype) || !is_numeric($itemtype)) {
        $itemtype = 0;
    }
    if (!isset($cid) || !is_numeric($cid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'category', 'admin', 'checkevent', 'Pubsub');
        throw new Exception($msg);
    }

    // Get datbase setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubeventstable = $xartable['pubsub_events'];

    // check this event isn't already in the DB
    $query = "SELECT id
              FROM  $pubsubeventstable
              WHERE modid = ?
              AND   itemtype = ?
              AND   cid = ?";
    $bindvars = array((int)$modid, (int)$itemtype, (int)$cid);
    if (isset($extra)) {
        $query .= ' AND extra = ?';
        array_push($bindvars, $extra);
    }
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // if event already exists then just return the event id;
    if (!$result->EOF) {
        list($id) = $result->fields;
        return $id;
    }

    if (!isset($extra)) {
        $extra = '';
    }

    if (empty($groupdescr) || !is_string($groupdescr)) {
        $groupdescr = xarML('Created by checkevent');
    }

    // Get next ID in table
    $id = $dbconn->GenId($pubsubeventstable);

    // Add item to events table
    $query = "INSERT INTO $pubsubeventstable (
              id,
              modid,
              itemtype,
              cid,
              extra,
              groupdescr)
            VALUES (?,?,?,?,?,?)";

    $bindvars = array((int)$id, (int)$modid, (int)$itemtype, (int)$cid, $extra, $groupdescr);
    $result = $dbconn->Execute($query, $bindvars);

    if (!$result) return;

    // Get the ID of the item that was inserted
    $id = $dbconn->PO_Insert_ID($pubsubeventstable, 'id');

    // return eventID
    return $id;

}

?>
