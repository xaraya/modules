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

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $ctable = &$xartable['comments_column'];

    $bind = array((int)$left, (int)$right, _COM_STATUS_ON, (int)$modid, (int)$objectid, (int)$itemtype);

    $sql = "SELECT P1.id, COUNT(P2.id) AS numitems"
        . " FROM $xartable[comments] AS P1, $xartable[comments] AS P2"
        . " WHERE P1.modid = P2.modid AND P1.itemtype = P2.itemtype AND P1.objectid = P2.objectid"
        . " AND P2.cleft >= P1.cleft AND P2.cleft <= P1.cright"
        . " AND P1.cleft >= ? AND P1.cright <= ?"
        . " AND P2.status = ?"
        . " AND P1.modid = ? AND P1.objectid = ? AND P1.itemtype = ?"
        . " GROUP BY P1.id";

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