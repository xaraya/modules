<?php

/**
 * Get total amount of site hits grouped by month
 *
 * @param   none
 * @return  mixed - array of data, sum and maximum OR false
 */
function stats_userapi_getpermonth()
{
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
              FROM $statstable
              GROUP BY xar_sta_month
              ORDER BY xar_sta_month";
    $result =& $dbconn->Execute($query);

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

	// free some memory
	unset($val);

	// return the items
	return array($data, $sum, $max);
}

?>