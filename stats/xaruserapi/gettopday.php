<?php

/**
 * Get date and amount of site hits on the best / worst day
 *
 * @param   string $type - 'best' or 'worst' day
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
              FROM $statstable
              GROUP BY xar_sta_year, xar_sta_month, xar_sta_day
              ORDER BY xar_sta_sum" . $sort . "";
    $result =& $dbconn->SelectLimit($query, 1);

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