<?php

/**
 * Get total amount of site hits grouped by weekday
 *
 * @param   none
 * @return  mixed - array of data, sum and maximum OR false
 */
function stats_userapi_getperweekday()
{
	// initialize variables
	$max = 0; $sum = 0;
	$data = array();
	for($i = 0; $i < 7; $i++) {
		$data[$i] = array('weekday' => $i,
						  'hits'    => 0);
	}

    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable     =& xarDBGetTables();
    $statstable   = $xartable['stats'];
    
    // create query
    $query = "SELECT xar_sta_weekday, SUM(xar_sta_hits) AS xar_sta_sum
              FROM $statstable
              GROUP BY xar_sta_weekday
              ORDER BY xar_sta_weekday";
    $result =& $dbconn->Execute($query);

    // Check for an error with the database code
	if (!$result) return;
	
    // generate the result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($weekday, $hits) = $result->fields;
        	if ($hits > $max) $max = $hits;
        	$sum += $hits;
        $data[$weekday] = array('weekday' => $weekday,
                                'hits'    => $hits);
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