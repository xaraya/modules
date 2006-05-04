<?php
/**
 * Count the number of forums in the database
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * @returns integer
 * @returns number of forums in the database
 * @param catids array List of category IDs
 * @param fid int Forum ID (why? it would always be zero or one)
 */

function xarbb_userapi_countforums($args)
{
    extract($args);

    // Security Check
    if (!xarSecurityCheck('ViewxarBB', 1, 'Forum')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xbbforumstable = $xartable['xbbforums'];

    $joins = array();
    $wheres = array();

    if (!empty($catids) && is_array($catids) && xarModIsHooked('categories', 'xarbb', 1)) {
        $categoriesdef = xarModAPIFunc('categories', 'user', 'leftjoin',
            array('cids' => $catids, 'modid' => xarModGetIDFromName('xarbb'))
        );

        if (!empty($categoriesdef)) {
            $join = ' LEFT JOIN ' . $categoriesdef['table'];
            $join .= ' ON ' . $categoriesdef['field'] . ' = xar_fid';
            if (!empty($categoriesdef['more'])) {
                $join .= $categoriesdef['more'];
            }
            $joins[] = $join;
            if (!empty($categoriesdef['where'])) {
                $wheres[] = $categoriesdef['where'];
            }
        }
    }

    if (isset($fid)) {
        $wheres[] = "xar_fid = " . (int)$fid;
    }

    if (count($wheres) > 0) {
        $where = " WHERE " . join(" AND ", $wheres)." ";
    } else {
        $where = "";
    }
    $join = " " . join(",", $joins) . " ";

    // Get links
    $query = "SELECT COUNT(1) FROM $xbbforumstable $join $where";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

?>