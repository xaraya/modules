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
 * delete a pubsub event
 * @param $args['eventid'] ID of the item
 * @returns bool
 * @return true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_delevent($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = [];
    if (!isset($eventid) || !is_numeric($eventid)) {
        $invalid[] = 'eventid';
    }
    if (count($invalid) > 0) {
        $msg = xarML(
            'Invalid #(1) in function #(3)() in module #(4)',
            join(', ', $invalid),
            'delevent',
            'Pubsub'
        );
        throw new Exception($msg);
    }

    // Security check
    if (!xarSecurity::check('DeletePubSub')) {
        return;
    }

    // Get datbase setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubeventstable = $xartable['pubsub_events'];

    // Delete item from events table
    $query = "DELETE FROM $pubsubeventstable
            WHERE eventid = ?";
    $dbconn->Execute($query, [(int)$eventid]);
    if (!$result) {
        return;
    }

    return true;
} // END delevent
