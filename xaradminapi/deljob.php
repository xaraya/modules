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
 * delete a pubsub job from the queue
 * @param $args['id'] ID of the job to delete
 * @returns bool
 * @return true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_deljob($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) function #(3)() in module #(4)',
                    join(', ',$invalid), 'deljob', 'Pubsub');
        throw new Exception($msg);
    }

    // Security check
    // TODO: Check this.  It doesn't make sense to me.  The schedular is probably being activated by an anonymous
    // process via CRON (or similiar) and won't be logged in.  Therefor, you would have to grant anonymous
    // delete access for these jobs.  That's just silly.
//    if (!xarSecurity::check('DeletePubSub', 1, 'item', "All:All:$id:All")) return;

    // Get datbase setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubprocesstable = $xartable['pubsub_process'];

    // Delete item
    $query = "DELETE FROM $pubsubprocesstable
              WHERE id = ?";
    $result = $dbconn->Execute($query, array((int)$id));
    if (!$result) return;

    return true;
}

?>
