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
 * Get amount of site hits of a specified day (in UTC)
 *
 * @param   int $year
 * @param   int $month
 * @param   int $day
 * @return  mixed  - amount of hits (int) OR false
 */
function stats_userapi_getday($args)
{
    // get arguments from argument array
    extract($args);

    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable     =& xarDBGetTables();
    $statstable   = $xartable['stats'];

    // create query
    $query = "SELECT SUM(xar_sta_hits), xar_sta_year, xar_sta_month, xar_sta_day
              FROM $statstable
              GROUP BY xar_sta_year, xar_sta_month, xar_sta_day
              HAVING xar_sta_year = $year
              AND xar_sta_month = $month
              AND xar_sta_day = $day";
    $result =& $dbconn->Execute($query);

    // check for an error with the database code
    if (!$result) return;

    // generate the result array
    $data = $result->fields[0];
    if (empty($data)) {
        $data = 0;
    }
    $result->Close();

    // return the items
    return $data;
}

?>