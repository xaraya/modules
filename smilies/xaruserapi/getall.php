<?php

/**
 * get all smilies
 * @returns array
 * @return array of links, or false on failure
 */
function smilies_userapi_getall($args)
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
	if(!xarSecurityCheck('OverviewSmilies')) {
        return $links;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $smiliestable = $xartable['smilies'];

    // Get links
    $query = "SELECT xar_sid,
                     xar_code,
                     xar_icon,
                     xar_emotion
            FROM $smiliestable
            ORDER BY xar_emotion";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($sid, $code, $icon, $emotion) = $result->fields;
        if (xarSecurityCheck('OverviewSmilies', 0)) {
            $links[] = array('sid'      => $sid,
                             'code'     => $code,
                             'icon'     => $icon,
                             'emotion'  => $emotion);
        }
    }

    $result->Close();

    return $links;
}
?>