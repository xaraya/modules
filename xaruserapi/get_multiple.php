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
 * @param integer    $id       (optional) the id of a comment
 * @param integer    $status    (optional) only pull comments with this status
 * @param integer    $author    (optional) only pull comments by this author
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

    if (!isset($id) || !is_numeric($id)) {
        $id = 0;
    } else {
        $nodelr = xarModAPIFunc('comments',
                                'user',
                                'get_node_lrvalues',
                                 array('id' => $id));
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

    // if the depth is zero then we
    // only want one comment
    $sql = "SELECT  $ctable[title] AS title,
                    $ctable[cdate] AS datetime,
                    $ctable[hostname] AS hostname,
                    $ctable[comment] AS text,
                    $ctable[author] AS author,
                    $ctable[author] AS uid,
                    $ctable[id] AS id,
                    $ctable[pid] AS pid,
                    $ctable[status] AS status,
                    $ctable[left] AS cleft,
                    $ctable[right] AS cright,
                    $ctable[postanon] AS postanon
              FROM  $xartable[comments]
             WHERE  $ctable[modid]=?
               AND  $ctable[status]=?";
    $bindvars = array();
    $bindvars[] = (int) $modid;
    $bindvars[] = (int) $status;

    if (isset($itemtype) && is_numeric($itemtype)) {
        $sql .= " AND $ctable[itemtype]=?";
        $bindvars[] = (int) $itemtype;
    }

    if (isset($objectid) && !empty($objectid)) {
        $sql .= " AND $ctable[objectid]=?";
        $bindvars[] = (string) $objectid; // yes, this is a string in the table
    }

    if (isset($author) && $author > 0) {
        $sql .= " AND $ctable[author] = ?";
        $bindvars[] = (int) $author;
    }

    if ($id > 0) {
        $sql .= " AND ($ctable[left] >= ?";
        $sql .= " AND  $ctable[right] <= ?)";
        $bindvars[] = (int) $nodelr['cleft'];
        $bindvars[] = (int) $nodelr['cright'];
    }

    if (!empty($reverse)) {
        $sql .= " ORDER BY $ctable[right] DESC";
    } else {
        $sql .= " ORDER BY $ctable[left]";
    }

// cfr. xarcachemanager - this approach might change later
    $expire = xarModGetVar('comments','cache.userapi.get_multiple');

    //Add select limit for modules that call this function and need Pager
    if (isset($numitems) && is_numeric($numitems)) {
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
        // FIXME Delete after date testing
        // $row['date'] = xarLocaleFormatDate("%B %d, %Y %I:%M %p",$row['datetime']);
        $row['date'] = $row['datetime'];
        $row['author'] = xarUserGetVar('name',$row['author']);
        comments_renderer_wrap_words($row['text'],80);
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
