<?php
/**
 * get all sitecloud
 * @returns array
 * @return array of links, or false on failure
 */
function sitecloud_userapi_getall($args)
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
	if(!xarSecurityCheck('Overviewsitecloud')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sitecloudtable = $xartable['sitecloud'];
    // Get links
    $query = "SELECT xar_id,
                     xar_title,
                     xar_url,
                     xar_string,
                     xar_date
            FROM $sitecloudtable";
    if (!empty($catid) && xarModIsHooked('categories','sitecloud')) {
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('cids' => array($catid),
                                            'modid' => xarModGetIDFromName('sitecloud')));
        if (!empty($categoriesdef)) {
            $query .= ' LEFT JOIN ' . $categoriesdef['table'];
            $query .= ' ON ' . $categoriesdef['field'] . ' = xar_id';
            if (!empty($categoriesdef['more'])) {
                $query .= $categoriesdef['more'];
            }
            if (!empty($categoriesdef['where'])) {
                $query .= ' WHERE ' . $categoriesdef['where'];
            }
        }
    }
    $query .= " ORDER BY xar_date DESC";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($id, $title, $url, $string, $date) = $result->fields;
        if (xarSecurityCheck('Overviewsitecloud')) {
            $links[] = array('id'      => $id,
                             'title'   => $title,
                             'url'     => $url,
                             'string'  => $string,
                             'date'    => $date);
        }
    }
    $result->Close();
    return $links;
}
?>
