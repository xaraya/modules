<?php
/**
 * get all bookmarks
 * @returns array
 * @return array of links, or false on failure
 */
function mybookmarks_userapi_getall($args)
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
    if(!xarSecurityCheck('Viewmybookmarks')) {
        return $links;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['mybookmarks'];
    // Get links
    $query = "SELECT xar_bm_id,
                     xar_bm_name,
                     xar_bm_url
            FROM $table
            WHERE xar_user_name = ?
            ORDER BY xar_bm_id";
    $bindvars = array($uid);
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($id, $name, $url) = $result->fields;
        if (xarSecurityCheck('Viewmybookmarks', 0)) {
            $links[] = array('id'      => $id,
                             'name'    => $name,
                             'url'     => $url);
        }
    }
    $result->Close();
    return $links;
}
?>