<?php

/**
 * get all smilies
 * @returns array
 * @return array of links, or false on failure
 */
function bbcode_userapi_getall($args)
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
    if(!xarSecurityCheck('OverviewBBCode')) {
        return $links;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['bbcode'];

    // Get links
    $query = "SELECT xar_id,
                     xar_tag,
                     xar_name,
                     xar_description,
                     xar_transformed
            FROM $table
            ORDER BY xar_id";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($id, $tag, $name, $description, $transformed) = $result->fields;
        if (xarSecurityCheck('OverviewBBCode', 0)) {
            $links[] = array('id'      => $id,
                             'tag'     => $tag,
                             'name'     => $name,
                             'description' => $description,
                             'transformed' => $transformed);
        }
    }
    $result->Close();
    return $links;
}
?>