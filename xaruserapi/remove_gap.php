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
 * Remove a gap in the celko tree
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   private
 * @param    integer    $startpoint    starting point for removing gap
 * @param    integer    $endpoint      end point for removing gap
 * @param    integer    $gapsize       the size of the gap to remove
 * @param    integer    $modid         the module id
 * @param    integer    $itemtype      the item type
 * @param    string     $objectid      the item id
 * @returns  integer    number of affected rows or false [0] on error
 */
function comments_userapi_remove_gap( $args )
{

    extract($args);

    if (!isset($startpoint)) {
        $msg = xarML('Missing or Invalid parameter \'startpoint\'!!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // 1 is used when a node is deleted and children are attached to the parent
    if (!isset($gapsize) || $gapsize < 1) {
        $gapsize = 2;
    }

    if (!isset($endpoint) || !is_numeric($endpoint)) {
        $endpoint = NULL;
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $sql_left  = "UPDATE $xartable[comments]
                     SET cleft = (cleft - $gapsize)
                   WHERE cleft > $startpoint";

    $sql_right = "UPDATE $xartable[comments]
                     SET cright = (cright - $gapsize)
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

    $result1 =& $dbconn->Execute($sql_left);
    $result2 =& $dbconn->Execute($sql_right);

    if(!$result1 || !$result2) {
        return;
    }

    return $dbconn->Affected_Rows();
}

?>
