<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * count number of items depending on additional module criteria
 *
 * @param array group
 * @return array number of items with descriptors
 */
function publications_adminapi_getstats($args)
{
    extract($args);

    $allowedfields = array('pubtype_id', 'state', 'owner', 'locale', 'pubdate_year', 'pubdate_month', 'pubdate_day');
    if (empty($group)) {
        $group = array();
    }
    $newfields = array();
    $newgroups = array();
    foreach ($group as $field) {
        if (empty($field) || !in_array($field,$allowedfields)) {
            continue;
        }
        if ($field == 'pubdate_year') {
            $dbtype = xarDB::getType();
            switch ($dbtype) {
                case 'mysql':
                    $newfields[] = "LEFT(FROM_UNIXTIME(start_date),4) AS myyear";
                    $newgroups[] = "myyear";
                    break;
                case 'postgres':
                    $newfields[] = "TO_CHAR(ABSTIME(start_date),'YYYY') AS myyear";
                // CHECKME: do we need to use TO_CHAR(...) for the group field too ?
                    $newgroups[] = "myyear";
                    break;
                case 'mssql':
                    $newfields[] = "LEFT(CONVERT(VARCHAR,DATEADD(ss,start_date,'1/1/1970'),120),4) as myyear";
                    $newgroups[] = "LEFT(CONVERT(VARCHAR,DATEADD(ss,start_date,'1/1/1970'),120),4)";
                    break;
                // TODO:  Add SQL queries for Oracle, etc.
                default:
                    continue 2;
            }
        } elseif ($field == 'pubdate_month') {
            $dbtype = xarDB::getType();
            switch ($dbtype) {
                case 'mysql':
                    $newfields[] = "LEFT(FROM_UNIXTIME(start_date),7) AS mymonth";
                    $newgroups[] = "mymonth";
                    break;
                case 'postgres':
                    $newfields[] = "TO_CHAR(ABSTIME(start_date),'YYYY-MM') AS mymonth";
                // CHECKME: do we need to use TO_CHAR(...) for the group field too ?
                    $newgroups[] = "mymonth";
                    break;
                case 'mssql':
                    $newfields[] = "LEFT(CONVERT(VARCHAR,DATEADD(ss,start_date,'1/1/1970'),120),7) as mymonth";
                    $newgroups[] = "LEFT(CONVERT(VARCHAR,DATEADD(ss,start_date,'1/1/1970'),120),7)";
                    break;
                // TODO:  Add SQL queries for Oracle, etc.
                default:
                    continue 2;
            }
        } elseif ($field == 'pubdate_day') {
            $dbtype = xarDB::getType();
            switch ($dbtype) {
                case 'mysql':
                    $newfields[] = "LEFT(FROM_UNIXTIME(start_date),10) AS myday";
                    $newgroups[] = "myday";
                    break;
                case 'postgres':
                    $newfields[] = "TO_CHAR(ABSTIME(start_date),'YYYY-MM-DD') AS myday";
                // CHECKME: do we need to use TO_CHAR(...) for the group field too ?
                    $newgroups[] = "myday";
                    break;
                case 'mssql':
                    $newfields[] = "LEFT(CONVERT(VARCHAR,DATEADD(ss,start_date,'1/1/1970'),120),10) as myday";
                    $newgroups[] = "LEFT(CONVERT(VARCHAR,DATEADD(ss,start_date,'1/1/1970'),120),10)";
                    break;
                // TODO:  Add SQL queries for Oracle, etc.
                default:
                    continue 2;
            }
        } else {
            $newfields[] = $field;
            $newgroups[] = $field;
        }
    }
    if (empty($newfields) || count($newfields) < 1) {
        $newfields = array('pubtype_id', 'state', 'owner');
        $newgroups = array('pubtype_id', 'state', 'owner');
    }

    // Database information
    $dbconn = xarDB::getConn();
    $xartables = xarDB::getTables();

    $query = 'SELECT ' . join(', ', $newfields) . ', COUNT(*)
              FROM ' . $xartables['publications'] . '
              GROUP BY ' . join(', ', $newgroups) . '
              ORDER BY ' . join(', ', $newgroups);

    $result = $dbconn->Execute($query);
    if (!$result) return;

    $stats = array();
    while (!$result->EOF) {
        if (count($newfields) > 3) {
            list($field1,$field2,$field3,$field4,$count) = $result->fields;
            $stats[$field1][$field2][$field3][$field4] = $count;
        } elseif (count($newfields) == 3) {
            list($field1,$field2,$field3,$count) = $result->fields;
            $stats[$field1][$field2][$field3] = $count;
        } elseif (count($newfields) == 2) {
            list($field1,$field2,$count) = $result->fields;
            $stats[$field1][$field2] = $count;
        } elseif (count($newfields) == 1) {
            list($field1,$count) = $result->fields;
            $stats[$field1] = $count;
        }
        $result->MoveNext();
    }
    $result->Close();

    return $stats;
}

?>