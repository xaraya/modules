<?php

/**
 * Get total hits
 *
 * Get all site hits that were recorded by the stats module
 *
 * @param   none
 * @return  int $data - total amount of site hits
 */
function stats_userapi_gettotalhits()
{
    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable     =& xarDBGetTables();
    $statstable   = $xartable['stats'];

    // create query
    $query = "SELECT SUM(xar_sta_hits)
              FROM $statstable";
    $result =& $dbconn->Execute($query);

    // check for an error with the database code
	if (!$result) return;

    // generate the result array
    $data = $result->fields[0];
    $result->Close();

    // return the items
    return $data;
}

?>