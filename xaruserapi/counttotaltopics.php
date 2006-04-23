<?php
/**
 * Count the number of topics for a given forum
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
 * @todo Merge this with serachtopics() since it does the same thing, only with a count(*) in the select part
*/
/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */

function xarbb_userapi_counttotaltopics($args)
{
    extract($args);

    // Optional argument
    if (!isset($startnum)) {
        $startnum = 1;
    }

    if (!isset($numitems)) {
        $numitems = -1;
    }

    if (!isset($where)) {
        $where = '';
    }

    if (!isset($wherevalue)){
        $wherevalue = '';
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];

    $bind = array();
    $query = "SELECT COUNT(1) FROM $xbbtopicstable ";

    if ($where <> '') {
        if ($where == 'replies') {
            // Searching for unanswered topics
            if (empty($wherevalue) || !is_numeric($wherevalue)) {
                // 0 - where there are no replies
                $query .= "WHERE xar_treplies = 0";
            } else {
                // >0 - where there are at least that number of replies
                $query .= "WHERE xar_treplies >= ?";
                $bind = (int)$wherevalue;
            }
        } elseif ($where == 'uid') {
            $query .= "WHERE xar_tposter = ?";
            $bind = (int)$wherevalue;
        } elseif ($where == 'from') {
            $query .= "WHERE xar_ttime >= ?";
            $bind = (int)$wherevalue;
        }
    }

    if (isset($numitems) && is_numeric($numitems)) {
        $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bind);
    } else {
        $result =& $dbconn->Execute($query, $bind);
    }

    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();
    return $numitems;
}

?>