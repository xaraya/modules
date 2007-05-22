<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Open a gap in the celko tree for inserting nodes
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   private
 * @param    integer    $startpoint    the point at wich the node will be inserted
 * @param    integer    $endpoint      end point for creating gap (used mostly for moving branches around)
 * @param    integer    $gapsize       the size of the gap to make (defaults to 2 for inserting a single node)
 * @param    integer    $modid         the module id
 * @param    integer    $itemtype      the item type
 * @param    string     $objectid      the item id
 * @returns  integer    number of affected rows or false [0] on error
 */
function comments_userapi_create_gap( $args )
{

    extract($args);

    if (!isset($startpoint)) {
        $msg = xarML('Missing or Invalid parameter \'startpoint\'!!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($endpoint) || !is_numeric($endpoint)) {
        $endpoint = NULL;
    }

    if (!isset($gapsize) || $gapsize <= 1) {
        $gapsize = 2;
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $sql_left  = "UPDATE $xartable[comments]
                     SET cleft = (cleft + $gapsize)
                   WHERE cleft > $startpoint";

    $sql_right = "UPDATE $xartable[comments]
                     SET cright = (cright + $gapsize)
                   WHERE cright >= $startpoint";

    // if we have an endpoint, use it :)
    if (!empty($endpoint) && $endpoint !== NULL) {
        $sql_left   .= " AND cleft <= $endpoint";
        $sql_right  .= " AND cright <= $endpoint";
    }
    // if we have a modid, use it
    if (!empty($modid)) {
        $sql_left   .= " AND modid = $modid";
        $sql_right  .= " AND modid = $modid";
    }
    // if we have an itemtype, use it (0 is acceptable too here)
    if (isset($itemtype)) {
        $sql_left   .= " AND itemtype = $itemtype";
        $sql_right  .= " AND itemtype = $itemtype";
    }
    // if we have an objectid, use it
    if (!empty($objectid)) {
        $sql_left   .= " AND objectid = '$objectid'";
        $sql_right  .= " AND objectid = '$objectid'";
    }

    // see if we support transactions here
    if ($dbconn->hasTransactions) {
        // try 3 times with increasing delay
        for ($i = 0; $i < 3; $i++) {
            if ($i > 0) {
                // sleep 10 msec the second time, 100 msec the third time
                $delay = 1000 * pow(10,$i);
                usleep($delay);
            }
            // start the transaction
            $dbconn->StartTrans();
            // note: we don't do explicit row locking here, because it takes longer
            //       and we end up with more deadlocks (ask the Postgres people why ?)
            // start by increasing the right side
            $result =& $dbconn->Execute($sql_right);
            if ($result) {
                // this should at least affect the parent
                $affected = $dbconn->Affected_Rows();
                // then increase the left side if necessary
                $result =& $dbconn->Execute($sql_left);
            }
            // if the transaction succeeded
            if ($dbconn->CompleteTrans()) {
                // return the number of affected rows
                return $affected;
            }
            // otherwise we roll back and try again
        }
        return;
    } else {
        // start by increasing the right side
        $result =& $dbconn->Execute($sql_right);
        if ($result) {
            // this should at least affect the parent
            $affected = $dbconn->Affected_Rows();
            // then increase the left side if necessary
            $result =& $dbconn->Execute($sql_left);
        }
        if (!$result) {
            return;
        }
        return $affected;
    }
}

?>
