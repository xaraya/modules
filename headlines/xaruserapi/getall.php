<?

/**
 * get all headlines
 * @returns array
 * @return array of links, or false on failure
 */

function headlines_userapi_getall($args)
{
    extract($args);

    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $links = array();

    // Security Check
	if(!xarSecurityCheck('OverviewHeadlines')) return;

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $headlinestable = $xartable['headlines'];

    // Get links
    $query = "SELECT xar_hid,
                     xar_title,
                     xar_desc,
                     xar_url,
                     xar_order
            FROM $headlinestable
            ORDER BY xar_order";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($hid, $title, $desc, $url, $order) = $result->fields;
        if (xarSecurityCheck('OverviewHeadlines')) {
            $links[] = array('hid'      => $hid,
                             'title'    => $title,
                             'desc'     => $desc,
                             'url'      => $url,
                             'order'    => $order);
        }
    }

    $result->Close();

    return $links;
}
?>