<?php

/**
 * Get total amount of site hits grouped by browser
 *
 * @param   bool $top10 - true, if only top 10 browsers are needed
 * @return  mixed - array of data, sum and maximum OR false
 */
function stats_userapi_getbybrowser($args)
{
    // get arguments from argument array
    extract($args);
    if (!isset($top10)) {
    	$top10 = false;
    }

	// initialize variables
	$max = 0; $sum = 0;
	$data = array();

	// get database setup
    $dbconn =& xarDBGetConn();
    $xartable     =& xarDBGetTables();
    $statstable   = $xartable['stats'];
    $sniffertable = $xartable['sniffer'];

    // create query
	// Exclude entries with xar_ua_agnam = '' cause they are bots and rss aggregators / blocks
    if ($top10 == true) {
        $query = "SELECT b.xar_ua_agnam AS name, 0, SUM(a.xar_sta_hits) as sum
                  FROM $statstable AS a, $sniffertable AS b
                  WHERE a.xar_ua_id = b.xar_ua_id
                  AND b.xar_ua_agnam <> ''
                  GROUP BY name
                  ORDER BY sum DESC";
	    $result =& $dbconn->SelectLimit($query,10);
	} else {
        $query = "SELECT b.xar_ua_agnam AS name, b.xar_ua_agver AS version, SUM(a.xar_sta_hits) as sum
                  FROM $statstable AS a, $sniffertable AS b
                  WHERE a.xar_ua_id = b.xar_ua_id
                  AND b.xar_ua_agnam <> ''
                  GROUP BY name, version
                  ORDER BY sum DESC";
    	$result =& $dbconn->Execute($query);
	}

    // check for an error with the database code
	
	if (!$result) return;

    // generate the result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($agent, $agver, $hits) = $result->fields;
        	if ($hits > $max) $max = $hits;
        	$sum += $hits;
        $data[] = compact('agent','agver','hits');
    }
    $result->Close();

    // return the items
    return array($data, $sum, $max);
}

?>