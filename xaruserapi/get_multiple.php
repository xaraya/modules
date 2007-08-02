<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Get a single comment or a list of comments. Depending on the parameters passed
 * you can retrieve either a single comment, a complete list of comments, a complete
 * list of comments down to a certain depth or, lastly, a specific branch of comments
 * starting from a specified root node and traversing the complete branch
 *
 * if you leave out the objectid, you -must- at least specify the author id
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access public
 * @param integer    $modid     the id of the module that these nodes belong to
 * @param integer    $itemtype  the item type that these nodes belong to
 * @param integer    $objectid  (optional) the id of the item that these nodes belong to
 * @param integer    $cid       (optional) the id of node to query (defaults to a root node)
 * @param integer    $status    (optional) only pull comments with this status
 * @param integer    $author    (optional) only pull comments by this author
 * @param bool       $onlydepth (optional) only get comments at this depth
 * @param integer    $depth     (optional) only get comments down to this depth
 * @param boolean    $reverse   (optional) reverse sort order from the database
 * @returns array     an array of comments or an empty array if no comments
 *                   found for the particular modid/objectid pair, or raise an
 *                   exception and return false.
 */
function comments_userapi_get_multiple($args)
{

    extract($args);

    if ( !isset($modid) || empty($modid) ) {
        $msg = xarML('Invalid #(1) [#(2)] for #(3) function #(4)() in module #(5)',
                                 'modid', $modid, 'userapi', 'get_multiple', 'comments');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                                        new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return false;
    }

    if (!isset($itemtype)) {
        $itemtype = 0;
    }
    if ( (!isset($objectid) || empty($objectid)) && !isset($author) ) {
        $msg = xarML('Invalid #(1) [#(2)] for #(3) function #(4)() in module #(5)',
                                 'objectid', $objectid, 'userapi', 'get_multiple', 'comments');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                                        new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return false;
    } else
        if (!isset($objectid) && isset($author)) {
            $objectid = 0;
    }

    if (!isset($cid) || !is_numeric($cid)) {
        $root = xarModAPIFunc('comments', 'user','get_node_root',
                       array('modid' => $modid,
                             'itemtype' => $itemtype,
                             'objectid' => $objectid));
        $cid = $root['xar_cid'];
    }

    // Optional argument for Pager -
    // for those modules that use comments and require this
     if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    if (!isset($status) || empty($status)) {
        $status = _COM_STATUS_ON;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ctable = &$xartable['comments_column'];

    // initialize the commentlist array
    $commentlist = array();

    // Get the field names and LEFT JOIN ... ON ... parts from users
    $usersdef = xarModAPIFunc('roles','user','leftjoin');

    $sql = "SELECT node.xar_cid,
                   node.xar_title,
                   node.xar_date,
                   node.xar_hostname,
                   node.xar_text,
                   $usersdef[name] AS xar_author,
                   node.xar_author AS xar_uid,
                   node.xar_pid,
                   node.xar_status,
                   node.xar_left,
                   node.xar_right,
                   node.xar_anonpost AS xar_postanon,
                   (COUNT(parent.xar_cid) - 1) AS depth
            FROM $xartable[comments] AS top
            JOIN $xartable[comments] AS node ON node.xar_left BETWEEN top.xar_left AND top.xar_right
            AND top.xar_modid = node.xar_modid
            AND top.xar_itemtype = node.xar_itemtype
            AND top.xar_objectid = node.xar_objectid
            AND top.xar_cid = ?
            JOIN $xartable[comments] AS parent
            ON node.xar_left BETWEEN parent.xar_left AND parent.xar_right
            AND parent.xar_modid = node.xar_modid
            AND parent.xar_itemtype = node.xar_itemtype
            AND parent.xar_objectid = node.xar_objectid
            AND node.xar_pid != 0
            AND node.xar_status = ?
            LEFT JOIN $usersdef[table] ON  $usersdef[field] = node.xar_author
            GROUP BY node.xar_cid";
    // if the depth is zero then we
    // only want one comment
    /*
    $sql = "SELECT  $ctable[title] AS xar_title,
                    $ctable[cdate] AS xar_date,
                    $ctable[hostname] AS xar_hostname,
                    $ctable[comment] AS xar_text,
                    $usersdef[name] AS xar_author,
                    $ctable[author] AS xar_uid,
                    $ctable[cid] AS xar_cid,
                    $ctable[pid] AS xar_pid,
                    $ctable[status] AS xar_status,
                    $ctable[left] AS xar_left,
                    $ctable[right] AS xar_right,
                    $ctable[postanon] AS xar_postanon
              FROM  $xartable[comments] LEFT JOIN $usersdef[table]
                ON  $usersdef[field] = $ctable[author]
             WHERE  $ctable[modid]=?
               AND  $ctable[status]=?";
    $bindvars = array();
    */

    $bindvars[] = (int) $cid;
    $bindvars[] = (int) $status;


    if (isset($author) && $author > 0) {
        $sql .= " AND $ctable[author] = ?";
        $bindvars[] = (int) $author;
    }

    if (isset($depth)) {
        if (isset($onlydepth) && $onlydepth) {
            $sql .= " HAVING depth = ?";
        } else {
            $sql .= " HAVING depth <= ?";
        }
        $bindvars[] = $depth;
    }
    if (!isset($order) || $order == _COM_SORT_DESC) {
        $sql .= " ORDER BY node.xar_right DESC";
    } else {
        $sql .= " ORDER BY node.xar_left";
    }

    // cfr. xarcachemanager - this approach might change later
    $expire = xarModGetVar('comments','cache.userapi.get_multiple');

    //Add select limit for modules that call this function and need Pager
    if (isset($numitems)) {
        if (!empty($expire)){
            $result =& $dbconn->CacheSelectLimit($expire, $sql, $numitems, $startnum-1,$bindvars);
        } else {
            $result =& $dbconn->SelectLimit($sql, $numitems, $startnum-1,$bindvars);
        }
    } else {
        if (!empty($expire)){
            $result =& $dbconn->CacheExecute($expire,$query,$bindvars);
        } else {
            $result =& $dbconn->Execute($query,$bindvars);
        }
    }

    if (!$result) return;

    // if we have nothing to return
    // we return nothing ;) duh? lol
    if ($result->EOF) {
        return array();
    }

    if (!xarModLoad('comments','renderer')) {
        $msg = xarML('Unable to load #(1) #(2)','comments','renderer');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UNABLE_TO_LOAD', new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return;
    }

    // zip through the list of results and
    // add it to the array we will return
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        // @todo either put this in a template, or make it conditional via parameter (modvar isn't enough)
        comments_renderer_wrap_words($row['xar_text'],80);
        $commentlist[] = $row;
        $result->MoveNext();
    }
    $result->Close();

    if (!comments_renderer_array_markdepths_bypid($commentlist)) {
        $msg = xarML('Unable to create depth by pid');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'SYSTEM_ERROR', new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return;
    }

    return $commentlist;
}
?>
