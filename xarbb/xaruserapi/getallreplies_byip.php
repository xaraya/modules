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
function xarbb_userapi_getallreplies_byip($args) {
    extract($args);

    if ( !isset($modid) || empty($modid) ) {
        $msg = xarML('Invalid #(1) [#(2)] for #(3) function #(4)() in module #(5)',
                                 'modid', $modid, 'userapi', 'get_multiple', 'comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                                        new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return false;
    }

    // Optional argument for Pager - 
    // for those modules that use comments and require this
     if (!isset($startnum)) {
        $startnum = 1;
    } 
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ctable = &$xartable['comments_column'];

    // initialize the commentlist array
    $commentlist = array();

    // if the depth is zero then we
    // only want one comment
    $sql = "SELECT  $ctable[hostname] AS xar_hostname,
                    $ctable[author] AS xar_uid
              FROM  $xartable[comments]
             WHERE  $ctable[modid]='$modid'";

    if (isset($hostname) && $hostname > 0) {
        $sql .= " AND $ctable[hostname] = '$hostname'";
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

    // zip through the list of results and
    // add it to the array we will return
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        $commentlist[] = $row;
        $result->MoveNext();
    }
    $result->Close();

    return $commentlist;
}

?>