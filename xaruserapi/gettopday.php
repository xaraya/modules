<?php
/**
 * Event API functions of Stats module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Stats Module
 * @link http://xaraya.com/index.php/release/34.html
 * @author Frank Besler <frank@besler.net>
 */
/**
 * Get date and amount of site hits on the best / worst day
 *
 * @param   $args['type'] string - 'best' or 'worst' day
 * @param   $args['year'] optional year
 * @param   $args['month'] optional month in year
 * @return  mixed  - date and amount of hits (array) OR false
 */
function stats_userapi_gettopday($args)
{
    // get arguments from argument array
    extract($args);

    // set the sort order (switch used for restrictive usage)
    switch ($type) {
        case 'best':
            $sort = ' DESC';
            break;
        case 'worst':
            $sort = '';
            break;
        default:
            return false;
    }

    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable     =& xarDBGetTables();
    $statstable   = $xartable['stats'];

    // create query
    $query = "SELECT xar_sta_year, xar_sta_month, xar_sta_day, SUM(xar_sta_hits) AS xar_sta_sum
              FROM $statstable ";
    $bindvars = array();
    if (!empty($year) && is_numeric($year)) {
        $query .= "WHERE xar_sta_year = ? ";
        $bindvars[] = $year;
        if (!empty($month) && is_numeric($month)) {
            $query .= "AND xar_sta_month = ? ";
            $bindvars[] = $month;
            //if (!empty($day) && is_numeric($day)) {
            //    $query .= "AND xar_sta_day = ? ";
            //    $bindvars[] = $day;
            //}
        }
    }
    if(!empty($userid) && is_numeric($userid)) {
        if(strpos("WHERE", $query)) {
            $query .= " AND xar_ua_id = ? ";
        } else {
            $query .= " WHERE xar_ua_id = ? ";
        }
        $bindvars[] = $userid;
    }
    $query .= "GROUP BY xar_sta_year, xar_sta_month, xar_sta_day
               ORDER BY xar_sta_sum" . $sort;
    $result =& $dbconn->SelectLimit($query, 1, -1, $bindvars);

    // check for an error with the database code
    if (!$result) return;

    // generate the result array
    list($year, $month, $day, $hits) = $result->fields;
    $result->Close();

    // return the items
    $data = array(compact('year','month','day'), $hits);
    return $data;
}

?>
