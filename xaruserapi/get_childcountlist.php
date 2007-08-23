<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Get the number of children comments for a list of comment ids
 *
 * @author mikespub
 * @access public
 * @param integer  $left the left limit for the list of comment ids
 * @param integer  $right the right limit for the list of comment ids
 * @param integer  $modid/$itemtype/$objectid of the module selected
 * @returns array  the number of child comments for each comment id,
 *                   or raise an exception and return false.
 */
function comments_userapi_get_childcountlist($args)
{

    extract($args);
    if (!isset($left) || !is_numeric($left) || !isset($right) || !is_numeric($right)) {
        $msg = xarML('Invalid #(1)', 'left/right');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return false;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ctable = &$xartable['comments_column'];

    if (!isset($status)) $status = _COM_STATUS_ON;
    $bind = array((int)$modid, (int)$objectid, (int)$itemtype, (int)$modid, (int)$objectid, (int)$itemtype, (int)$left, (int)$right, $status);

    $sql = "SELECT P1.xar_cid, COUNT(P2.xar_cid) AS numitems"
        . " FROM (SELECT * FROM $xartable[comments] WHERE xar_modid = ? AND xar_objectid = ? AND xar_itemtype = ?) AS P1, (SELECT * FROM $xartable[comments] WHERE xar_modid = ? AND xar_objectid = ? AND xar_itemtype = ?) AS P2"
        . " WHERE P1.xar_modid = P2.xar_modid AND P1.xar_itemtype = P2.xar_itemtype AND P1.xar_objectid = P2.xar_objectid"
        . " AND P2.xar_left >= P1.xar_left AND P2.xar_left <= P1.xar_right"
        . " AND P1.xar_left >= ? AND P1.xar_right <= ?"
        . " AND P2.xar_status >= ?"
        . " AND P2.xar_pid != 0"
        . " GROUP BY P1.xar_cid";

    $result =& $dbconn->Execute($sql, $bind);
    if (!$result) return;

    if ($result->EOF) return array();

    $count = array();
    while (!$result->EOF) {
        list($id, $numitems) = $result->fields;
        // return total count - 1 ... the -1 is so we don't count the comment root.
        $count[$id] = $numitems - 1;
        $result->MoveNext();
    }
    $result->Close();

    return $count;
}
?>