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

    $query = "SELECT COUNT(1) FROM $xbbtopicstable ";

    if ($where <> '') {
        if ($where=='replies') {
            //searching for unanswered topics
            $query .= "WHERE xar_treplies = '0'";
        } elseif ($where == 'uid') {
            $query .= "WHERE xar_tposter = '{$wherevalue}'";
        } elseif ($where == 'from') {
            $query .= "WHERE xar_ttime > '{$wherevalue}'";
        }
    }

    if (isset($numitems) && is_numeric($numitems)) {
        $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    } else {
        $result =& $dbconn->Execute($query);
    }

    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();
    return $numitems;
}

?>