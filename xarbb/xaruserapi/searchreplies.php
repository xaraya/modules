<?php

/**
 * Searches all active comments based on a set criteria
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function xarbb_userapi_searchreplies($args) 
{

    if (empty($args) || count($args) < 1) {
        return;
    }

    extract($args);
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ctable = &$xartable['comments_column'];
    $where = '';

    // initialize the commentlist array
    $commentlist = array();

    $sql = "SELECT  $ctable[title] AS xar_title,
                    $ctable[cdate] AS xar_date,
                    $ctable[author] AS xar_author,
                    $ctable[cid] AS xar_cid,
                    $ctable[pid] AS xar_pid,
                    $ctable[left] AS xar_left,
                    $ctable[right] AS xar_right,
                    $ctable[postanon] AS xar_postanon,
                    $ctable[modid]  AS xar_modid,
                    $ctable[itemtype]  AS xar_itemtype,
                    $ctable[objectid] as xar_objectid
              FROM  $xartable[comments]
             WHERE  $ctable[modid] = 300
               AND  (";

    if (isset($title)) {
        $sql .= "$ctable[title] LIKE '$title'";
    }

    if (isset($text)) {
        if (isset($title)) {
            $sql .= " OR ";
        }
        $sql .= "$ctable[comment] LIKE '$text'";
    }

    if (isset($author)) {
        if (isset($title) || isset($text)) {
            $sql .= " OR ";
        }
        if ($author == 'anonymous') {
            $sql .= " $ctable[author] = '$uid' OR $ctable[postanon] = '1'";
        } else {
            $sql .= " $ctable[author] = '$uid' AND $ctable[postanon] != '1'";
        }
    }

    $sql .= ") ORDER BY $ctable[left]";

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
        $row['xar_name'] = xarUserGetVar('name',$row['xar_author']);
        $commentlist[] = $row;
        $result->MoveNext();
    }
    $result->Close();
    return $commentlist;
}
?>