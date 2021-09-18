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
 * Get the number of children comments for a list of comment ids
 * @author mikespub
 * @access public
 * @param integer  $left the left limit for the list of comment ids
 * @param integer  $right the right limit for the list of comment ids
 * @param integer  $moduleid/$itemtype/$itemid of the module selected
 * @returns array  the number of child comments for each comment id,
 *                   or raise an exception and return false.
 */
function comments_userapi_get_childcountlist($args)
{
    extract($args);

    if (!isset($left) || !is_numeric($left) || !isset($right) || !is_numeric($right)) {
        $msg = xarML('Invalid #(1)', 'left/right');
        throw new BadParameterException($msg);
    }

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();

    $bind = [(int)$left, (int)$right, _COM_STATUS_ON, (int)$moduleid, (int)$itemid, (int)$itemtype];

    $sql = "SELECT P1.id, COUNT(P2.id) AS numitems"
        . " FROM $xartable[comments] AS P1, $xartable[comments] AS P2"
        . " WHERE P1.module_id = P2.module_id AND P1.itemtype = P2.itemtype AND P1.itemid = P2.itemid"
        . " AND P2.left_id >= P1.left_id AND P2.left_id <= P1.right_id"
        . " AND P1.left_id >= ? AND P1.right_id <= ?"
        . " AND P2.status = ?"
        . " AND P1.module_id = ? AND P1.itemid = ? AND P1.itemtype = ?"
        . " GROUP BY P1.id";

    $result = $dbconn->Execute($sql, $bind);
    if (!$result) {
        return;
    }

    if ($result->EOF) {
        return [];
    }

    $count = [];
    while (!$result->EOF) {
        [$id, $numitems] = $result->fields;
        // return total count - 1 ... the -1 is so we don't count the comment root.
        $count[$id] = $numitems - 1;
        $result->MoveNext();
    }
    $result->Close();

    return $count;
}
