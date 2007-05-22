<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
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
    $dbconn = xarDB::getConn();

    // Get the field names and LEFT JOIN ... ON ... parts from articles
    // By passing on the $args, we can let leftjoin() create the WHERE for
    // the articles-specific columns too now
    $articlesdef = xarModAPIFunc('articles','user','leftjoin',$args);

    // Create the query base query
    $query = "SELECT $articlesdef[aid], $articlesdef[title], $articlesdef[pubtypeid], $articlesdef[authorid]
                FROM $articlesdef[table] WHERE ";

    // we rely on leftjoin() to create the necessary articles clauses now
    if (!empty($articlesdef['where'])) {
        $query .= " $articlesdef[where] AND ";
    }

    // Get the current article
    $current = xarModAPIFunc('articles','user','get',array('aid' => $aid));

    // Create the ORDER BY part
    switch($sort) {
    case 'title':
        $query .= $articlesdef['title'] . ' < ' . $dbconn->qstr($current['title']) . ' ORDER BY ' . $articlesdef['title'] . ' DESC, ' . $articlesdef['aid'] . ' DESC';
        break;
    case 'aid':
        $query .= $articlesdef['aid'] . ' < ' . $current['aid'] . ' ORDER BY ' . $articlesdef['aid'] . ' DESC';
        break;
    case 'date':
    default:
        $query .= $articlesdef['pubdate'] . ' < ' . $dbconn->qstr($current['pubdate']) . ' ORDER BY ' . $articlesdef['pubdate'] . ' DESC, ' . $articlesdef['aid'] . ' DESC';
    }


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
