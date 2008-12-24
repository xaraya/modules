<?php
/**
 * Messages module
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage messages
 * @link http://xaraya.com/index.php/release/6.html
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
 * @returns  integer    number of affected rows or false [0] on error
 */

sys::import('modules.messages.xarincludes.defines');

function messages_userapi_create_gap( $args )
{

    extract($args);

    if (!isset($startpoint)) {
        $msg = xarML('Missing or Invalid parameter \'startpoint\'!!');
        throw new BadParameterException($msg);
    }

    if (!isset($endpoint) || !is_numeric($endpoint)) {
        $endpoint = NULL;
    }

    if (!isset($gapsize) || $gapsize <= 1) {
        $gapsize = 2;
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $sql_left  = "UPDATE $xartable[messages]
                     SET left_id = (left_id + $gapsize)
                   WHERE left_id > $startpoint";

    $sql_right = "UPDATE $xartable[messages]
                     SET right_id = (right_id + $gapsize)
                   WHERE right_id >= $startpoint";

    // if we have an endpoint, use it :)
    if (!empty($endpoint) && $endpoint !== NULL) {
        $sql_left   .= " AND left_id <= $endpoint";
        $sql_right  .= " AND right_id <= $endpoint";
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
