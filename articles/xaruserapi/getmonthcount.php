<?php

/**
 * count the number of items per month
 * @param $args['cids'] not supported here (yet ?)
 * @param $args['ptid'] publication type ID we're interested in
 * @param $args['status'] array of requested status(es) for the articles
 * @returns array
 * @return array(month => count), or false on failure
 */
function articles_userapi_getmonthcount($args)
{
    // Get database setup
    list($dbconn) = xarDBGetConn();

    // Get the field names and LEFT JOIN ... ON ... parts from articles
    // By passing on the $args, we can let leftjoin() create the WHERE for
    // the articles-specific columns too now
    $articlesdef = xarModAPIFunc('articles','user','leftjoin',$args);

// TODO: check how to do this for non-mysql databases !
    $query = 'SELECT LEFT(FROM_UNIXTIME(xar_pubdate),7) as mymonth, COUNT(*) FROM ' . $articlesdef['table'];
    if (!empty($articlesdef['where'])) {
        $query .= ' WHERE ' . $articlesdef['where'];
    }
    $query .= ' GROUP BY mymonth';
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $months = array();
    while (!$result->EOF) {
        list($month, $count) = $result->fields;
        $months[$month] = $count;
        $result->MoveNext();
    }

    return $months;
}

?>
