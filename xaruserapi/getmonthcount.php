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
 * count the number of items per month
 * @param $args['cids'] not supported here (yet ?)
 * @param $args['ptid'] publication type ID we're interested in
 * @param $args['status'] array of requested status(es) for the articles
 * @return array array(month => count), or false on failure
 */
function articles_userapi_getmonthcount($args)
{
    // Get database setup
    $dbconn = xarDB::getConn();

    // Get the field names and LEFT JOIN ... ON ... parts from articles
    // By passing on the $args, we can let leftjoin() create the WHERE for
    // the articles-specific columns too now
    $articlesdef = xarModAPIFunc('articles', 'user', 'leftjoin', $args);

    // Bug 1590 - Create custom query supported by each database.
    $dbtype = xarDBGetType();

    // If categories have been passed in, then join them in.
    if (!empty($args['cids'])) {
        // Get the LEFT JOIN ... ON ...  and WHERE (!) parts from categories
        // This function supports itemtype arrays, so pass in ptids.
        $categoriesdef = xarModAPIFunc(
            'categories', 'user', 'leftjoin',
            array(
                'cids' => $args['cids'],
                'andcids' => true, //$andcids,
                'itemtype' => (isset($args['ptid']) ? $args['ptid'] : null),
                'modid' => xarModGetIDFromName('articles'),
            )
        );

        if (empty($categoriesdef)) return;
    }

    // Select distinct if we are limited by category.
    $distinct = !empty($args['cids']) ? 'DISTINCT ' : '';

    // TODO:  Add SQL queries for Oracle, etc.
    switch ($dbtype) {
        case 'mysql':
            $select = 'LEFT(FROM_UNIXTIME(xar_pubdate),7) AS mymonth, COUNT(' . $distinct . 'xar_aid)';
            $from = $articlesdef['table'];
            break;
        case 'postgres':
            $select = 'TO_CHAR(ABSTIME(xar_pubdate),\'YYYY-MM\') AS mymonth, COUNT(' . $distinct . 'xar_aid)';
            $from = $articlesdef['table'];
            break;
        case 'mssql':
            $select = 'LEFT(CONVERT(VARCHAR,DATEADD(ss,xar_pubdate,\'1/1/1970\'),120),7) as mymonth, COUNT(' . $distinct . 'xar_aid)';
            $from = $articlesdef['table'];
            break;
        default:
            return;
    }

    $where = array();

    if (!empty($articlesdef['where'])) {
        $where[] = $articlesdef['where'];
    }

    switch ($dbtype) {
        case 'mssql':
            $groupby = 'LEFT(CONVERT(VARCHAR,DATEADD(ss,xar_pubdate,\'1/1/1970\'),120),7)';
            break;
        default:
            $groupby = 'mymonth';
            break;
    }

    if (!empty($args['cids'])) {
        // add this for SQL compliance when there are multiple JOINs
        // bug 4429: sqlite doesnt like the parentheses
        if ($dbconn->databaseType != 'sqlite') $from = '(' . $from . ')';

        // Add the LEFT JOIN ... ON ... parts from categories
        $from .= ' INNER JOIN ' . $categoriesdef['table'];
        $from .= ' ON ' . $categoriesdef['field'] . ' = ' . $articlesdef['aid'];

        if (!empty($categoriesdef['more']) && $dbconn->databaseType != 'sqlite') {
            $from = '(' . $from . ') ' . $categoriesdef['more'];
        }

        $where[] = $categoriesdef['where'];
    }

    // Put the query together.
    $query = 'SELECT ' . $select . ' FROM ' . $from;
    if (!empty($where)) $query .= ' WHERE ' . implode(' AND ', $where);
    if (!empty($groupby)) $query .= ' GROUP BY ' . $groupby;

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
