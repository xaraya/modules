<?php

/**
 * get the number of articles per publication type
 * @param $args['status'] array of requested status(es) for the articles
 * @returns array
 * @return array(id => count), or false on failure
 */
function articles_userapi_getpubcount($args)
{
    static $pubcount = array();

    if (count($pubcount) > 0) {
        return $pubcount;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();

    // Get the LEFT JOIN ... ON ...  and WHERE parts from articles
    $articlesdef = xarModAPIFunc('articles','user','leftjoin',$args);

    $query = 'SELECT ' . $articlesdef['pubtypeid'] . ', COUNT(*)
            FROM ' . $articlesdef['table'];
    if (!empty($articlesdef['where'])) {
        $query .= ' WHERE ' . $articlesdef['where'];
    }
    $query .= ' GROUP BY ' . $articlesdef['pubtypeid'];
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    if ($result->EOF) {
        return $pubcount;
    }
    while (!$result->EOF) {
        list($id, $count) = $result->fields;
        $pubcount[$id] = $count;
        $result->MoveNext();
    }

    return $pubcount;
}

?>
