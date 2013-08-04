<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
        throw new BadParameterException($msg);
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
                     SET left_id = (left_id - $gapsize)
                   WHERE left_id > $startpoint";

    $sql_right = "UPDATE $xartable[comments]
                     SET right_id = (right_id - $gapsize)
                   WHERE right_id >= $startpoint";

    // if we have an endpoint, use it :)
    if (!empty($endpoint) && $endpoint !== NULL) {
        $sql_left   .= " AND left_id <= $endpoint";
        $sql_right  .= " AND right_id <= $endpoint";
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
