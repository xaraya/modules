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
 * update an existing pubsub event
 * @param $args['eventid'] the ID of the item
 * @param $args['module'] the new module name of the item
 * @param $args['eventtype'] the new event type of the item
 * @param $args['groupdescr'] the new group description of the item
 * @param $args['actionid'] the new action id for the item
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_updateevent($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = [];
    if (!isset($eventid) || !is_numeric($eventid)) {
        $invalid[] = 'eventid';
    }
    if (!isset($modid) || !is_numeric($modid)) {
        $invalid[] = 'module';
    }
    if (!isset($itemtype) || !is_numberic($itemtype)) {
        $invalid[] = 'eventtype';
    }
    if (!isset($groupdescr) || !is_string($groupdescr)) {
        $invalid[] = 'groupdescr';
    }
    //if (!isset($actionid) || !is_numeric($actionid)) {
    //    $invalid[] = 'actionid';
    //}
    if (count($invalid) > 0) {
        $msg = xarML(
            'Invalid #(1) function #(3)() in module #(4)',
            join(', ', $invalid),
            'updateevent',
            'Pubsub'
        );
        throw new Exception($msg);
    }

    // Security check
    if (!xarSecurity::check('EditPubSub', 1, 'item', "All:$eventid:All:All")) {
        return;
    }

    // Get database setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubeventstable = $xartable['pubsub_events'];

    // Update the item
    $query = "UPDATE $pubsubeventstable
              SET modid = ?,
                  itemtype = ?,
                  groupdescr = ?
              WHERE eventid = ?";
    $bindvars = [(int)$module, $itemtype, $groupdescr, (int)$eventid];
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) {
        return;
    }

    return true;
} // END updateevent
