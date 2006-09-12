<?php
/*
 * Search extension IDs
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Searches all active comments based on a set criteria
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 * @deprecated ? This is not used afaik (jojodee)
 */
function release_userapi_searchids($args) 
{
    if (empty($args) || count($args) < 1) {
        return;
    }

    $modid = '';

    extract($args);
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ctable = &$xartable['release_id'];
    $where = '';
    // initialize the commentlist array
    $commentlist = array();
    $sql = "SELECT  xar_rid,
                    xar_uid,
                    xar_displayname,
                    xar_tstatus,
                    xar_ttime
              FROM  $ctable
              WHERE  (";

    $bindvars = array();

    if (isset($rid)) {
        $sql .= "xar_rid LIKE ?";
        $bindvars[] = $rid;
    }

    if (isset($displname)) {
        if (isset($rid)) {
            $sql .= " OR ";
        }
        $sql .= "xar_tpost LIKE ?";
        $bindvars[] = $displname;
    }

    if (isset($desc)) {
        if (isset($rid) || isset($displayname)) {
            $sql .= " OR ";
        }
        $sql .= " xar_tposter = ?";
        $bindvars[] = $desc;
    }

    $sql .= ")  ORDER BY xar_ttime DESC";

    $result =& $dbconn->Execute($sql, $bindvars);
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
        $row['xar_uid'] = xarUserGetVar('name',$row['xar_uid']);
        $commentlist[] = $row;
        $result->MoveNext();
    }

    $result->Close();
    return $commentlist;
}
?>