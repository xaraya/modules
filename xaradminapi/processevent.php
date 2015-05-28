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
 * process a pubsub event, by adding a job for each subscriber to the process queue
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
    $invalid = array();
    if (empty($modid) || !is_numeric($modid)) {
        $invalid[] = 'modid';
    }
    if (!isset($cid) || !is_numeric($cid)) {
        $invalid[] = 'cid';
    }
    if (!isset($objectid) || !is_numeric($objectid)) {
        $invalid[] = 'objectid';
    }
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'processevent', 'Pubsub');
        throw new Exception($msg);
    }

    if (empty($itemtype) || !is_numeric($itemtype)) {
        $itemtype = 0;
    }

    // Security check - not via hooks
//    if (!xarSecurityCheck('AddPubSub')) return;

    // Get datbase setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubeventstable  = $xartable['pubsub_events'];
    $pubsubregtable     = $xartable['pubsub_reg'];
    $pubsubprocesstable = $xartable['pubsub_process'];

    // Create an array to list the subscriptions that need to be processed.
    $markSubscriptions = array();

    $includechildren = xarModVars::get('pubsub','includechildren');
    if ( !empty($cid) && $includechildren == 1 )
    {
        $ancestors = xarMod::apiFunc('categories','user','getancestors'
                                   , array('cid'=>$cid,'order'=>'self') );
        $ancestors = array_keys($ancestors);

        $query = "SELECT pubsubid, cid
                    FROM $pubsubeventstable, $pubsubregtable
                   WHERE $pubsubeventstable.eventid = $pubsubregtable.eventid
                     AND $pubsubeventstable.modid =?
                     AND $pubsubeventstable.itemtype = ?";
//                      . "
//                     AND $pubsubeventstable. = " . ($cid);
        $bindvars = array((int)$modid, (int)$itemtype);
        if (isset($extra)) {
            $query .= " AND $pubsubeventstable.extra = ?";
            array_push($bindvars, $extra);
        }
        $result =& $dbconn->Execute($query, $bindvars);
        if (!$result) return;

        for (; !$result->EOF; $result->MoveNext())
        {
            list($pubsubid, $cid) = $result->fields;

            if( $cid == $cid || in_array($cid, $ancestors))
            {
                $markSubscriptions[] = $pubsubid;
            }
        }


    } else {
        $query = "SELECT pubsubid
                    FROM $pubsubeventstable, $pubsubregtable
                   WHERE $pubsubeventstable.eventid = $pubsubregtable.eventid
                     AND $pubsubeventstable.modid = ?
                     AND $pubsubeventstable.itemtype = ?
                     AND $pubsubeventstable.cid = ?";
        $bindvars = array((int)$modid, (int)$itemtype, (int)$cid);
        if (isset($extra)) {
            $query .= " AND $pubsubeventstable.extra = ?";
            array_push($bindvars, $extra);
        }
        $result =& $dbconn->Execute($query, $bindvars);
        if (!$result) return;

        for (; !$result->EOF; $result->MoveNext())
        {
            list($pubsubid) = $result->fields;

            $markSubscriptions[] = $pubsubid;
        }
    }

    foreach( $markSubscriptions as $pubsubid )
    {
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
        $result2 =& $dbconn->Execute($query, $bindvars);
        if (!$result2) return;
    }

    return true;

} // END processevent

?>
