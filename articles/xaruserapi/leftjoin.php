<?php

/**
 * return the field names and correct values for querying (or joining on)
 * the articles table
 * example 1 : SELECT ..., $title, $body,...
 *             FROM $table
 *             WHERE $title LIKE 'Hello world%'
 *                 AND $where
 *
 * example 2 : SELECT ..., $title, $body,...
 *             FROM ...
 *             LEFT JOIN $table
 *                 ON $field = <name of articleid field in your module>
 *             WHERE ...
 *                 AND $title LIKE 'Hello world%'
 *                 AND $where
 *
 * Note : the following arguments are all optional :
 *
 * @param $args['aids'] optional array of aids that we are selecting on
 * @param $args['authorid'] the ID of the author
 * @param $args['ptid'] publication type ID (for news, sections, reviews, ...)
 * @param $args['status'] array of requested status(es) for the articles
 * @param $args['search'] search text parameter(s)
 * @param $args['startdate'] articles published at startdate or later
 *                           (unix timestamp format)
 * @param $args['enddate'] articles published before enddate
 *                         (unix timestamp format)
 * @returns array
 * @return array('table' => 'nuke_articles',
 *               'field' => 'nuke_articles.xar_aid',
 *               'where' => 'nuke_articles.xar_aid IN (...)',
 *               'title'  => 'nuke_articles.xar_title',
 *               ...
 *               'body'  => 'nuke_articles.xar_body')
 */
function articles_userapi_leftjoin($args)
{
    // Get arguments from argument array
    extract($args);

    // Optional argument
    if (!isset($aids)) {
        $aids = array();
    }

    // Note : no security checks here

    // Table definition
    $xartable = xarDBGetTables();
    $articlestable = $xartable['articles'];

    $leftjoin = array();

    // Add available columns in the articles table (for now)
    $columns = array('aid','title','summary','authorid','pubdate','pubtypeid',
                     'notes','status','body');
    foreach ($columns as $column) {
        $leftjoin[$column] = $articlestable . '.xar_' . $column;
    }

    // Specify LEFT JOIN ... ON ... [WHERE ...] parts
    $leftjoin['table'] = $articlestable;
    $leftjoin['field'] = $leftjoin['aid'];

    // Specify the WHERE part
    $where = array();
    if (!empty($authorid) && is_numeric($authorid)) {
        $where[] = $leftjoin['authorid'] . ' = ' . $authorid;
    }
    if (!empty($ptid) && is_numeric($ptid)) {
        $where[] = $leftjoin['pubtypeid'] . ' = ' . $ptid;
    }
    if (!empty($status) && is_array($status)) {
        if (count($status) == 1 && is_numeric($status[0])) {
            $where[] = $leftjoin['status'] . ' = ' . $status[0];
        } elseif (count($status) > 1) {
            $allstatus = join(', ',$status);
            $where[] = $leftjoin['status'] . ' IN (' . $allstatus . ')';
        }
    }
    if (!empty($startdate) && is_numeric($startdate)) {
        $where[] = $leftjoin['pubdate'] . ' >= ' . $startdate;
    }
    if (!empty($enddate) && is_numeric($enddate)) {
        $where[] = $leftjoin['pubdate'] . ' < ' . $enddate;
    }
    if (count($aids) > 0) {
        $allaids = join(', ', $aids);
        $where[] = $articlestable . '.xar_aid IN (' .
                   xarVarPrepForStore($allaids) . ')';
    }
    if (!empty($search)) {
// TODO : improve + make use of full-text indexing for recent MySQL versions ?
        // 1. find quoted text
        $normal = array();
        if (preg_match_all('#"(.*?)"#',$search,$matches)) {
            foreach ($matches[1] as $match) {
                $normal[] = $match;
                $search = trim(preg_replace('#"$match"#','',$search));
            }
        }
        if (preg_match_all("/'(.*?)'/",$search,$matches)) {
            foreach ($matches[1] as $match) {
                $normal[] = $match;
                $search = trim(preg_replace("#'$match'#",'',$search));
            }
        }
        // 2. find mandatory +text to include
        // 3. find mandatory -text to exclude
        // 4. find normal text
        $more = preg_split('/\s+/',$search,-1,PREG_SPLIT_NO_EMPTY);
        $normal = array_merge($normal,$more);
        $find = array();
        foreach ($normal as $text) {
            $text = xarVarPrepForStore($text);
        // TODO: use XARADODB to escape wildcards (and use portable ones) ??
            $text = preg_replace('/%/','\%',$text);
            $text = preg_replace('/_/','\_',$text);
            $find[] = $leftjoin['title'] . " LIKE '%" . $text . "%'";
            $find[] = $leftjoin['summary'] . " LIKE '%" . $text . "%'";
            $find[] = $leftjoin['body'] . " LIKE '%" . $text . "%'";
        }
        $where[] = '(' . join(' OR ',$find) . ')';
    }
    if (count($where) > 0) {
        $leftjoin['where'] = join(' AND ', $where);
    } else {
        $leftjoin['where'] = '';
    }

    return $leftjoin;
}

?>
