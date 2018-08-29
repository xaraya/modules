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
 * update an existing pubsub job
 * @param $args['id'] the ID of this job
 * @param $args['pubsubid'] the new pubsub id for this job
 * @param $args['objectid'] the new object id for this job
 * @param $args['id'] the template id for this job
 * @param $args['status']   the new status for this job
 * @returns bool
 * @return true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_updatejob($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }
    if (!isset($pubsubid) || !is_numeric($pubsubid)) {
        $invalid[] = 'pubsubid';
    }
    if (!isset($objectid) || !is_numeric($objectid)) {
        $invalid[] = 'objectid';
    }
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }
    if (!isset($status) || !is_string($status)) {
        $invalid[] = 'status';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) function #(3)() in module #(4)',
                    join(', ',$invalid), 'updatejob', 'Pubsub');
        throw new Exception($msg);
    }

    // Security check
    if (!xarSecurityCheck('EditPubSub', 1, 'item', "All:All:$id:All")) return;

    // Get database setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubprocesstable = $xartable['pubsub_process'];

    // Update the item
    $query = "UPDATE $pubsubprocesstable
              SET pubsubid = ?,
                  objectid = ?,
                  status = ?
            WHERE id = ?";
        $bindvars = array((int)$pubsubid, (int)$objectid, $status, $id);
        $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    return true;
}

?>
