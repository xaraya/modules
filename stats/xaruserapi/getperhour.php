<?php

/**
 * Get total amount of site hits grouped by hour
 *
 * @param   none
 * @return  mixed - array of data, sum and maximum OR false
 */
function stats_userapi_getperhour()
{
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
              FROM $statstable
              GROUP BY xar_sta_hour
              ORDER BY xar_sta_hour";
    $result =& $dbconn->Execute($query);

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

	// free some memory
	unset($val);

	// return the items
	return array($data, $sum, $max);
}

?>