<?php
/*
 *
 * Keywords Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @author mikespub
*/

/**
 * get list of keywords (from the existing assignments for now)
 *
 * @param $args['count'] if you want to count items per keyword
 */
function keywords_userapi_getlist($args)
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $keywordstable = $xartable['keywords'];

    // Get count per keyword from the database
    if (!empty($args['count'])) {
        $query = "SELECT xar_keyword, COUNT(xar_id)
                  FROM $keywordstable
                  GROUP BY xar_keyword
                  ORDER BY xar_keyword ASC";
        $result =& $dbconn->Execute($query);
        if (!$result) return;

        $items = array();
        if ($result->EOF) {
            $result->Close();
            return $items;
        }
        while (!$result->EOF) {
            list($word,$count) = $result->fields;
            $items[$word] = $count;
            $result->MoveNext();
        }
        $result->Close();
        return $items;
    }

    // Get distinct keywords from the database
    $query = "SELECT DISTINCT xar_keyword
              FROM $keywordstable
              ORDER BY xar_keyword ASC";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $items = array();
    $items[''] = '';
    if ($result->EOF) {
        $result->Close();
        return $items;
    }
    while (!$result->EOF) {
        list($word) = $result->fields;
        $items[$word] = $word;
        $result->MoveNext();
    }
    $result->Close();
    return $items;
}

?>
