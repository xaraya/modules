<?php

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

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $headlinestable = $xartable['headlines'];

    // Get links
    $query = "SELECT xar_hid,
                     xar_title,
                     xar_desc,
                     xar_url,
                     xar_order
            FROM $headlinestable";

    if (!empty($catid) && xarModIsHooked('categories','headlines')) {
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('cids' => array($catid),
                                            'modid' => xarModGetIDFromName('headlines')));
        if (!empty($categoriesdef)) {
            $query .= ' LEFT JOIN ' . $categoriesdef['table'];
            $query .= ' ON ' . $categoriesdef['field'] . ' = xar_hid';
            if (!empty($categoriesdef['more'])) {
                $query .= $categoriesdef['more'];
            }
            if (!empty($categoriesdef['where'])) {
                $query .= ' WHERE ' . $categoriesdef['where'];
            }
        }
    }

    $query .= " ORDER BY xar_order";

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
