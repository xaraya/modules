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
 * Get total amount of site hits grouped by hour
 *
 * @param   $args['year'] optional year
 * @param   $args['month'] optional month in year
 * @param   $args['day'] optional day in month
 * @return  mixed - array of data, sum and maximum OR false
 */
function stats_userapi_getperhour($args)
{
    extract($args);

    // initialize variables
    $max = 0; $sum = 0;
    $data = array();
    for ($i=0; $i<=23; $i++) {
        $data[$i] = array('hour' => $i,
                          'hits' => 0);
    }

    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable     =& xarDBGetTables();
    $statstable   = $xartable['stats'];

    // Get per hour
    $query = "SELECT xar_sta_hour, SUM(xar_sta_hits) AS xar_sta_sum
              FROM $statstable ";
    $bindvars = array();
    if (!empty($year) && is_numeric($year)) {
        $query .= "WHERE xar_sta_year = ? ";
        $bindvars[] = $year;
        if (!empty($month) && is_numeric($month)) {
            $query .= "AND xar_sta_month = ? ";
            $bindvars[] = $month;
            if (!empty($day) && is_numeric($day)) {
                $query .= "AND xar_sta_day = ? ";
                $bindvars[] = $day;
            }
        }
    }
    $query .= "GROUP BY xar_sta_hour
               ORDER BY xar_sta_hour";
    $result =& $dbconn->Execute($query, $bindvars);

    // check for an error with the database code
    if (!$result) return;

    // generate the result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($hour, $hits) = $result->fields;
        if ($hits > $max) $max = $hits;
        $sum += $hits;
        $data[$hour] = array('hour' => $hour,
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
