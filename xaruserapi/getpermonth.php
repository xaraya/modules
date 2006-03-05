<?php
/**
 * Event API functions of Stats module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Stats Module
 * @link http://xaraya.com/index.php/release/34.html
 * @author Frank Besler <frank@besler.net>
 */
/**
 * Get total amount of site hits grouped by month
 *
 * @param   $args['year'] optional year
 * @return  mixed - array of data, sum and maximum OR false
 */
function stats_userapi_getpermonth($args)
{
    extract($args);

    // initialize variables
    $max = 0; $sum = 0;
    $data = array();
    for ($i=1; $i<=12; $i++) {
        $data[$i] = array('month' => $i,
                          'hits'  => 0);
    }

    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable     =& xarDBGetTables();
    $statstable   = $xartable['stats'];

    // create query
    $query = "SELECT xar_sta_month, SUM(xar_sta_hits) AS xar_sta_sum
              FROM $statstable ";
    $bindvars = array();
    if (!empty($year) && is_numeric($year)) {
        $query .= "WHERE xar_sta_year = ? ";
        $bindvars[] = $year;
    }
    $query .= "GROUP BY xar_sta_month
               ORDER BY xar_sta_month";
    $result =& $dbconn->Execute($query, $bindvars);

    // check for an error with the database code
    if (!$result) return;

    // generate the result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($month, $hits) = $result->fields;
        if ($hits > $max) $max = $hits;
        $sum += $hits;
        $data[$month] = array('month' => $month,
                              'hits'  => $hits);
    }
    $result->Close();

    // prevent divbyzero errors
    if ($sum == 0) $sum = 1;
    if ($max == 0) $max = 1;

    // return the items
    return array($data, $sum, $max);
}

?>
