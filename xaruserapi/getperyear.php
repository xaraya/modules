<?php

/**
 * Get total amount of site hits grouped by year
 *
 * @param   none
 * @return  mixed - array of data, sum and maximum OR false
 */
function stats_userapi_getperyear($args)
{
    extract($args);

    // initialize variables
    $max = 0; $sum = 0;
    $data = array();

    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable     =& xarDBGetTables();
    $statstable   = $xartable['stats'];

    // create query
    $query = "SELECT xar_sta_year, SUM(xar_sta_hits) AS xar_sta_sum
              FROM $statstable
              GROUP BY xar_sta_year
              ORDER BY xar_sta_year";
    $result =& $dbconn->Execute($query);

    // check for an error with the database code
    if (!$result) return;
    
    // generate the result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($year, $hits) = $result->fields;
        if ($hits > $max) $max = $hits;
        $sum += $hits;
        $data[$year] = array('year' => $year,
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
