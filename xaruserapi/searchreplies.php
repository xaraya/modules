<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
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

    // TODO: hard-coded '300'?
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
               AND $ctable[pid] != 0
               AND  (";
               
    $bindvars = array();

    if (isset($title)) {
        $sql .= "$ctable[title] LIKE ?";
        $bindvars[] = $title;
    }

    if (isset($text)) {
        if (isset($title)) {
            $sql .= " OR ";
        }
        $sql .= "$ctable[comment] LIKE ?";
        $bindvars[] = $text;
    }

    if (isset($author)) {
        if (isset($title) || isset($text)) {
            $sql .= " OR ";
        }

        if ($author == 'anonymous') {
            $sql .= " $ctable[author] = ? OR $ctable[postanon] = ?";
            $bindvars[] = $uid;
            $bindvars[] = 1;
        } else {
            $sql .= " $ctable[author] = ? AND $ctable[postanon] != ?";
            $bindvars[] = $uid;
            $bindvars[] = 1;
        }
    }

    $sql .= ") ORDER BY $ctable[left]";

    $result =& $dbconn->Execute($sql, $bindvars);
    if (!$result) return;

    // If we have nothing to return, then return an empty array.
    if ($result->EOF) return array();

    // zip through the list of results and
    // add it to the array we will return
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        $row['xar_name'] = xarUserGetVar('name', $row['xar_author']);
        $commentlist[] = $row;
        $result->MoveNext();
    }

    $result->Close();
    return $commentlist;
}

?>