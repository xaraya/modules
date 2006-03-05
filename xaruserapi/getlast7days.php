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
 * Get hits of last 7 days (today included)
 *
 * Get total amount of site hits during the seven previous days
 *
 * @param   none
 * @return  mixed - array of data, sum and maximum OR false
 */
function stats_userapi_getlast7days()
{
    // initialize variables
    $max = 0; $sum = 0;
    $data = array();

    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable     =& xarDBGetTables();
    $statstable   = $xartable['stats'];

    // create query
    $query = "SELECT xar_sta_year, xar_sta_month, xar_sta_day, SUM(xar_sta_hits) AS xar_sta_sum
              FROM $statstable
              GROUP BY xar_sta_year, xar_sta_month, xar_sta_day
              ORDER BY xar_sta_year DESC, xar_sta_month DESC, xar_sta_day DESC";
    $result =& $dbconn->SelectLimit($query,7);

    // check for an error with the database code
    if (!$result) return;

    // generate the result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($year, $month, $day, $hits) = $result->fields;
        if ($hits > $max) $max = $hits;
        $sum += $hits;
        $data[] = compact('year','month','day','hits');
    }
    $result->Close();

    // prevent divbyzero errors
    if ($sum == 0) $sum = 1;
    if ($max == 0) $max = 1;

    // free some memory
    unset($val);

    // return the items
    return array($data, $sum, $max);
}

?>
