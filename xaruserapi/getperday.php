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
 * Get total amount of site hits grouped by day
 *
 * @param   $args['year'] optional year
 * @param   $args['month'] optional month in year
 * @return  mixed - array of data, sum and maximum OR false
 */
function stats_userapi_getperday($args)
{
    extract($args);

    // initialize variables
    $max = 0; $sum = 0;
    $data = array();

    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable     =& xarDBGetTables();
    $statstable   = $xartable['stats'];

    // Get per hour
    $query = "SELECT xar_sta_day, SUM(xar_sta_hits) AS xar_sta_sum
              FROM $statstable ";
    $bindvars = array();
    if (!empty($year) && is_numeric($year)) {
        $query .= "WHERE xar_sta_year = ? ";
        $bindvars[] = $year;
        if (!empty($month) && is_numeric($month)) {
            $query .= "AND xar_sta_month = ? ";
            $bindvars[] = $month;
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
    $query .= "GROUP BY xar_sta_day
               ORDER BY xar_sta_day";
    $result =& $dbconn->Execute($query, $bindvars);

    // check for an error with the database code
    if (!$result) return;

    // generate the result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($day, $hits) = $result->fields;
        if ($hits > $max) $max = $hits;
        $sum += $hits;
        $data[$day] = array('day' => $day,
                            'hits' => $hits);
    }
    $result->Close();

    // prevent divbyzero errors
    if ($sum == 0) $sum = 1;
    if ($max == 0) $max = 1;

    // return the items
    return array($data, $sum, $max);
}

?>
