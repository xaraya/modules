<?php

/**
 * get previous article
 * Note : the following parameters are all optional (except aid and ptid)
 *
 * @param $args['aid'] the article ID we want to have the previous article of
 * @param $args['ptid'] publication type ID (for news, sections, reviews, ...)
 * @param $args['sort'] sort order ('date','title','hits','rating',...)
 * @param $args['authorid'] the ID of the author
 * @param $args['status'] array of requested status(es) for the articles
 * @param $args['enddate'] articles published before enddate
 *                         (unix timestamp format)
 * @returns array
 * @return array of article fields, or false on failure
 */
function articles_userapi_getprevious($args)
{
    // Get arguments from argument array
    extract($args);

    // Optional argument
    if (empty($sort)) {
        $sort = 'date';
    }
    if (!isset($status)) {
        // frontpage or approved
        $status = array(3,2);
    }

    // Default fields in articles (for now)
    $fields = array('aid','title');

    // Security Check
    if (!xarSecurityCheck('ViewArticles')) return;

    // Database information
    $dbconn =& xarDBGetConn();

    // Get the field names and LEFT JOIN ... ON ... parts from articles
    // By passing on the $args, we can let leftjoin() create the WHERE for
    // the articles-specific columns too now
    $articlesdef = xarModAPIFunc('articles','user','leftjoin',$args);

    // Create the query
    $query = "SELECT $articlesdef[aid], $articlesdef[title], $articlesdef[pubtypeid], $articlesdef[authorid]
                FROM $articlesdef[table]
               WHERE $articlesdef[aid] < " . xarVarPrepForStore($aid);

    // we rely on leftjoin() to create the necessary articles clauses now
    if (!empty($articlesdef['where'])) {
        $query .= " AND $articlesdef[where]";
    }

/*
// TODO: make this configurable too someday ?
    // Create the ORDER BY part
    if ($sort == 'title') {
        $query .= ' ORDER BY ' . $articlesdef['title'] . ' ASC, ' . $articlesdef['aid'] . ' DESC';
    } elseif ($sort == 'hits' && !empty($hitcountdef['hits'])) {
        $query .= ' ORDER BY ' . $hitcountdef['hits'] . ' DESC, ' . $articlesdef['aid'] . ' DESC';
    } elseif ($sort == 'rating' && !empty($ratingsdef['rating'])) {
        $query .= ' ORDER BY ' . $ratingsdef['rating'] . ' DESC, ' . $articlesdef['aid'] . ' DESC';
    } else { // default is 'date'
        $query .= ' ORDER BY ' . $articlesdef['pubdate'] . ' DESC, ' . $articlesdef['aid'] . ' DESC';
    }
*/

    $query .= ' ORDER BY ' . $articlesdef['aid'] . ' DESC';

    // Run the query - finally :-)
    $result =& $dbconn->SelectLimit($query, 1, 0);

    if (!$result) return;

    $item = array();
    list($item['aid'],$item['title'],$item['pubtypeid'],$item['authorid']) = $result->fields;

    $result->Close();

// TODO: grab categories & check against them too

    // check security - don't generate an exception here
    if (!xarSecurityCheck('ViewArticles',0,'Article',"$item[pubtypeid]:All:$item[authorid]:$item[aid]")) {
        return array();
    }

    return $item;
}

?>
