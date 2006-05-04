<?php

/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 * 
 * @subpackage  xarbb Module
 * @author John Cox, jojodee
*/
/**
 * Get a single comment or a list of comments for a forum. Depending on the parameters passed
 * you can retrieve either a single comment, a complete list of comments, a complete
 * list of comments down to a certain depth or, lastly, a specific branch of comments
 * starting from a specified root node and traversing the complete branch
 *
 * if you leave out the objectid, you -must- at least specify the author id
 *
 * @author original Carl P. Corliss (aka rabbitt)
 * @author Jo Dalle Nogare (jojodee)
 * @access public
 * @param integer    $modid     the id of the module that these nodes belong to
 * @param integer    $itemtype  the item type that these nodes belong to
 * @param integer    $objectid  (optional) the id of the item that these nodes belong to
 * @param integer    $cid       (optional) the id of a comment
 * @param integer    $status    (optional) only pull comments with this status
 * @param integer    $author    (optional) only pull comments by this author
 * @returns array     an array of comments or an empty array if no comments
 *                   found for the particular modid/objectid pair, or raise an
 *                   exception and return false.
 */
function xarbb_userapi_get_allposts($args)
{
    extract($args);
    $modid=xarModGetIDFromName('xarbb');

    if (!isset($modid) || empty($modid)) {
        $msg = xarML('Invalid #(1) [#(2)]', 'modid', $modid);
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return false;
    }

    if ((!isset($objectid) || empty($objectid)) && !isset($author)) {
        $msg = xarML('Invalid #(1) [#(2)]', 'objectid', $objectid);
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return false;
    } elseif (!isset($objectid) && isset($author)) {
        $objectid = 0;
    }

    if (!isset($cid) || !is_numeric($cid)) {
        $cid = 0;
    } else {
        $nodelr = xarModAPIFunc('comments', 'user', 'get_node_lrvalues', array('cid' => $cid));
    }

    // Optional argument for Pager - 
    // for those modules that use comments and require this
    if (!isset($startnum)) $startnum = 1;
    if (!isset($numitems)) $numitems = -1;
    if (!isset($status) || empty($status)) $status = 2;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // initialize the commentlist array
    $commentlist = array();

    // if the depth is zero then we
    // only want one comment
    
    $sql = "SELECT  c_tab.xar_title AS xar_title,
                    c_tab.xar_date AS xar_datetime,
                    c_tab.xar_hostname,
                    c_tab.xar_text,
                    c_tab.xar_author,
                    c_tab.xar_author AS xar_uid,
                    c_tab.xar_cid,
                    c_tab.xar_pid,
                    c_tab.xar_status,
                    c_tab.xar_left,
                    c_tab.xar_right,
                    c_tab.xar_anonpost AS xar_postanon
              FROM  $xartable[comments] AS c_tab
             WHERE  c_tab.xar_modid = ? AND c_tab.xar_objectid = ?
               AND  c_tab.xar_status = ?";

    // objectid is still a string for now
    $bindvars = array((int) $modid, (string) $objectid, (int) $status);

    if (isset($itemtype) && is_numeric($itemtype)) {
        $sql .= " AND c_tab.xar_itemtype = ?";
        $bindvars[] = (int) $itemtype;
    }

    if (isset($author) && $author > 0) {
        $sql .= " AND c_tab.xar_author = ?";
        $bindvars[] = (int) $author;
    }

    if ($cid > 0) {
        $sql .= " AND (c_tab.xar_left >= ?";
        $bindvars[] = (int) $nodelr['xar_left'];
        $sql .= " AND c_tab.xar_right <= ?)";
        $bindvars[] =  (int) $nodelr['xar_right'];
    }

    $sql .= " ORDER BY c_tab.xar_date DESC";

    //Add select limit for modules that call this function and need Pager
    if (isset($numitems) && is_numeric($numitems)) {
        $result =& $dbconn->SelectLimit($sql, $numitems, $startnum-1, $bindvars);
    } else {
        $result =& $dbconn->Execute($query,$bindvars);
    }

    //$result =& $dbconn->Execute($sql);
    if (!$result) return;

    // Nothing to return, so return an empty array.
    if ($result->EOF) {
        return array();
    }

    if (!xarModLoad('comments', 'renderer')) {
        $msg = xarML('Unable to load #(1) #(2)', 'comments', 'renderer');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UNABLE_TO_LOAD', new SystemException($msg));
        return;
    }

    // zip through the list of results and
    // add it to the array we will return
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        // $row['xar_date'] = xarLocaleFormatDate("%B %d, %Y %I:%M %p",$row['xar_datetime']);
        $row['xar_datetime'] = $row['xar_datetime'];
        $row['xar_author'] = xarUserGetVar('name',$row['xar_author']);
        comments_renderer_wrap_words($row['xar_text'],80);
        $commentlist[] = $row;
        $result->MoveNext();
    }
    $result->Close();

    if (!comments_renderer_array_markdepths_bypid($commentlist)) {
        $msg = xarML('Unable to create depth by pid');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'SYSTEM_ERROR', new SystemException($msg));
        return;
    }

    return $commentlist;
}

?>