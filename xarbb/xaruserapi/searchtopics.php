<?php

/**
 * Searches all active comments based on a set criteria
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function xarbb_userapi_searchtopics($args) 
{
    if (empty($args) || count($args) < 1) {
        return;
    }

    $modid = '';

    extract($args);
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ctable = &$xartable['xbbtopics'];
    $where = '';
    // initialize the commentlist array
    $commentlist = array();
    $sql = "SELECT  xar_ttitle,
                    xar_tid,
                    xar_tposter,
                    xar_tstatus,
                    xar_ttime
              FROM  $ctable
              WHERE  (";

    if (isset($title)) {
        $sql .= "xar_ttitle LIKE '$title'";
    }

    if (isset($text)) {
        if (isset($title)) {
            $sql .= " OR ";
        }
        $sql .= "xar_tpost LIKE '$text'";
    }

    if (isset($author)) {
        if (isset($title) || isset($text)) {
            $sql .= " OR ";
        }
        $sql .= " xar_tposter = '$uid'";
    }

    $sql .= ")  ORDER BY xar_ttime DESC";

    $result =& $dbconn->Execute($sql);
    if (!$result) return;

    // if we have nothing to return
    // we return nothing ;) duh? lol
    if ($result->EOF) {
        return array();
    }

    // zip through the list of results and
    // add it to the array we will return
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        $row['xar_author'] = xarUserGetVar('name',$row['xar_tposter']);
        $commentlist[] = $row;
        $result->MoveNext();
    }

    $result->Close();
    return $commentlist;
}
?>