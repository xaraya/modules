<?

/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */
function sitecloud_userapi_countitems()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Security Check
	if(!xarSecurityCheck('Overviewsitecloud')) return;
    $sitecloudtable = $xartable['sitecloud'];
    $query = "SELECT COUNT(1)
            FROM $sitecloudtable";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    list($numitems) = $result->fields;
    $result->Close();
    return $numitems;
}
?>