<?php

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
 * @param integer    $cid       (optional) the id of a comment
 * @param integer    $status    (optional) only pull comments with this status
 * @param integer    $author    (optional) only pull comments by this author
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
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                                        new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return false;
    }

    if ( (!isset($objectid) || empty($objectid)) && !isset($author) ) {
        $msg = xarML('Invalid #(1) [#(2)] for #(3) function #(4)() in module #(5)',
                                 'objectid', $objectid, 'userapi', 'get_multiple', 'comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                                        new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return false;
    } else
        if (!isset($objectid) && isset($author)) {
            $objectid = 0;
    }

    if (!isset($cid) || !is_numeric($cid)) {
        $cid = 0;
    } else {
        $nodelr = xarModAPIFunc('comments',
                                'user',
                                'get_node_lrvalues',
                                 array('cid' => $cid));
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
    $sql = "SELECT  $ctable[title] AS xar_title,
                    $ctable[cdate] AS xar_datetime,
                    $ctable[hostname] AS xar_hostname,
                    $ctable[comment] AS xar_text,
                    $ctable[author] AS xar_author,
                    $ctable[author] AS xar_uid,
                    $ctable[cid] AS xar_cid,
                    $ctable[pid] AS xar_pid,
                    $ctable[status] AS xar_status,
                    $ctable[left] AS xar_left,
                    $ctable[right] AS xar_right,
                    $ctable[postanon] AS xar_postanon
              FROM  $xartable[comments]
             WHERE  $ctable[modid]='$modid'
               AND  $ctable[status]='$status'";

    if (isset($itemtype) && is_numeric($itemtype)) {
        $sql .= " AND $ctable[itemtype]='$itemtype'";
    }

    if (isset($objectid) && !empty($objectid)) {
        $sql .= " AND $ctable[objectid]='$objectid'";
    }

    if (isset($author) && $author > 0) {
        $sql .= " AND $ctable[author] = '$author'";
    }

    if ($cid > 0) {
        $sql .= " AND ($ctable[left] >= $nodelr[xar_left]";
        $sql .= " AND  $ctable[right] <= $nodelr[xar_right])";
    }


    $sql .= " ORDER BY $ctable[left]";

    //Add select limit for modules that call this function and need Pager
    if (isset($numitems) && is_numeric($numitems)) {
        $result =& $dbconn->SelectLimit($sql, $numitems, $startnum-1);
    } else {
       $result =& $dbconn->Execute($query);
    }
    //$result =& $dbconn->Execute($sql);
    if (!$result) return;

    // if we have nothing to return
    // we return nothing ;) duh? lol
    if ($result->EOF) {
        return array();
    }

    if (!xarModLoad('comments','renderer')) {
        $msg = xarML('Unable to load #(1) #(2)','comments','renderer');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNABLE_TO_LOAD', new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return;
    }

    // zip through the list of results and
    // add it to the array we will return
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        $row['xar_date'] = xarLocaleFormatDate("%B %d, %Y %I:%M %p",$row['xar_datetime']);
        $row['xar_author'] = xarUserGetVar('name',$row['xar_author']);
        comments_renderer_wrap_words($row['xar_text'],80);
        $commentlist[] = $row;
        $result->MoveNext();
    }
    $result->Close();

    if (!comments_renderer_array_markdepths_bypid($commentlist)) {
        $msg = xarML('Unable to create depth by pid');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'SYSTEM_ERROR', new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return;
    }

    return $commentlist;
}

?>
