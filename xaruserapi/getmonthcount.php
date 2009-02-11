<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * count the number of items per month
 * @param $args['cids'] not supported here (yet ?)
 * @param $args['ptid'] publication type ID we're interested in
 * @param $args['state'] array of requested status(es) for the publications
 * @return array array(month => count), or false on failure
 */
function publications_userapi_getmonthcount($args)
{
    // Get database setup
    $dbconn = xarDB::getConn();

    // Get the field names and LEFT JOIN ... ON ... parts from publications
    // By passing on the $args, we can let leftjoin() create the WHERE for
    // the publications-specific columns too now
    $publicationsdef = xarModAPIFunc('publications','user','leftjoin',$args);

    // Bug 1590 - Create custom query supported by each database.
    $dbtype = xarDBGetType();
    switch ($dbtype) {
        case 'mysql':
            $query = "SELECT LEFT(FROM_UNIXTIME(pubdate),7) AS mymonth, COUNT(*) FROM " . $publicationsdef['table'];
            break;
        case 'postgres':
            $query = "SELECT TO_CHAR(ABSTIME(pubdate),'YYYY-MM') AS mymonth, COUNT(*) FROM " . $publicationsdef['table'];
            break;
        case 'mssql':
            $query = "SELECT LEFT(CONVERT(VARCHAR,DATEADD(ss,pubdate,'1/1/1970'),120),7) as mymonth, COUNT(*) FROM " . $publicationsdef['table'];
            break;
        // TODO:  Add SQL queries for Oracle, etc.
        default:
            return;
    }
    if (!empty($publicationsdef['where'])) {
        $query .= ' WHERE ' . $publicationsdef['where'];
    }
    switch ($dbtype) {
        case 'mssql':
            $query .= " GROUP BY LEFT(CONVERT(VARCHAR,DATEADD(ss,pubdate,'1/1/1970'),120),7)";
            break;
        default:
            $query .= ' GROUP BY mymonth';
            break;
    }
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
