<?php

/**
 * Adds a comment to the database based on the objectid/modid pair
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer     $args['modid']      the module id
 * @param    integer     $args['itemtype']   the item type
 * @param    string      $args['objectid']   the item id
 * @param    integer     $args['pid']        the parent id
 * @param    string      $args['title']    the title (title) of the comment
 * @param    string      $args['comment']    the text (body) of the comment
 * @param    integer     $args['postanon']   whether or not this post is gonna be anonymous
 * @param    integer     $args['author']     user id of the author (for API access)
 * @param    string      $args['hostname']   hostname (for API access)
 * @param    datetime    $args['date']       date of the comment (for API access)
 * @param    integer     $args['cid']        comment id (for API access - import only)
 * @returns  integer     the id of the new comment
 */
function comments_userapi_add($args) {
    extract($args);

    if (!isset($modid)) {
        $modid = xarVarCleanFromInput('modid');
        if (empty($modid)) {
            $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                                     'modid', 'userapi', 'add', 'comments');
            xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return;
        }
    }

    if (empty($itemtype) || !is_numeric($itemtype)) {
        $itemtype = 0;
    }

    if (!isset($objectid)) {
        $objectid = xarVarCleanFromInput('objectid');
        if (empty($objectid)) {
            $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                                     'objectid', 'userapi', 'add', 'comments');
            xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return;
        }
    }

    if (!isset($pid)) {
        $pid = xarVarCleanFromInput('pid');
        if (empty($pid)) $pid = 0;
    }

    if (!isset($title)) {
        $title = xarVarCleanFromInput('title');
        if (empty($title)) {
            $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                                     'title', 'userapi', 'add', 'comments');
            xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return;
        }
    }

    if (!isset($comment)) {
        $comment = xarVarCleanFromInput('comment');
        if (empty($comment)) {
            $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                                     'comment text', 'userapi', 'add', 'comments');
            xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return;
        }
    }

    if (!isset($postanon)) {
        $postanon = xarVarCleanFromInput('postanon');
        if (empty($postanon)) $postanon = 0;
    }

    if (!isset($author)) {
        $author = xarUserGetVar('uid');
    }

    if (!isset($hostname)) {
        $forwarded = xarServerGetVar('HTTP_X_FORWARDED_FOR');
        if (!empty($forwarded)) {
            $hostname = preg_replace('/,.*/', '', $forwarded);
        } else {
            $hostname = xarServerGetVar('REMOTE_ADDR');
        }
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ctable = &$xartable['comments_column'];

    // parentid == zero then we need to find the root nodes
    // left and right values cuz we're adding the new comment
    // as a top level comment
    if ($pid == 0) {
        $root_lnr = xarModAPIFunc('comments','user','get_node_root',array('modid' => $modid, 'objectid' => $objectid, 'itemtype' => $itemtype));

        // ok, if the there was no root left and right values then
        // that means this is the first comment for this particular
        // modid/objectid combo -- so we need to create a dummy (root)
        // comment from which every other comment will branch from
        if (!count($root_lnr)) {
            $pid = xarModAPIFunc('comments','user','add_rootnode', array('modid'    => $modid,
                                                                          'objectid' => $objectid, 
                                                                          'itemtype' => $itemtype));
        } else {
            $pid = $root_lnr['xar_cid'];
        }
    }

    // pid should now always have a value
    assert($pid!=0 && !empty($pid));

    // grab the left and right values from the parent
    $parent_lnr = xarModAPIFunc('comments',
                                'user',
                                'get_node_lrvalues',
                                 array('cid' => $pid));
    
    // there should be -at-least- one affected row -- if not
    // then raise an exception. btw, at the very least,
    // the 'right' value of the parent node would have been affected.
    if (!xarModAPIFunc('comments',
                       'user',
                       'create_gap',
                        array('startpoint' => $parent_lnr['xar_right']))) {
            
            $msg  = xarML('Unable to create gap in tree for comment insertion! Comments table has possibly been corrupted.');
            $msg .= xarML('Please seek help on the public-developer list xaraya_public-dev@xaraya.com, or in the #support channel on Xaraya\'s IRC network.');
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNABLE_TO_LOAD', new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
            return;
    }

    // the comment's date will be autoinserted by database
    $cdate    = time();
    $left     = $parent_lnr['xar_right'];
    $right    = $left + 1;
    $status   = xarModGetVar('comments','AuthorizeComments') ? _COM_STATUS_OFF : _COM_STATUS_ON;

    if (!isset($id)) {
        $id = $dbconn->GenId($xartable['comments']);
    }

    $sql = "INSERT INTO $xartable[comments]
                (xar_cid,
                 xar_modid,
                 xar_itemtype,
                 xar_objectid,
                 xar_author,
                 xar_title,
                 xar_date,
                 xar_hostname,
                 xar_text,
                 xar_left,
                 xar_right,
                 xar_pid,
                 xar_status,
                 xar_anonpost)
          VALUES ("
        .xarVarPrepForStore($id).",'"
        .xarVarPrepForStore($modid)."','"
        .xarVarPrepForStore($itemtype)."','"
        .xarVarPrepForStore($objectid)."','"
        .xarVarPrepForStore($author)."','"
        .xarVarPrepForStore($title)."',"
        . (isset($date) ? "'".xarVarPrepForStore($date)."'" : "'".xarVarPrepForStore($cdate)."'") . ",'"
        .xarVarPrepForStore($hostname)."','"
        .xarVarPrepForStore($comment)."','"
        .xarVarPrepForStore($left)."','"
        .xarVarPrepForStore($right)."','"
        .xarVarPrepForStore($pid)."','"
        .xarVarPrepforStore($status)."','"
        . (empty($postanon) ? 0 : 1) ."')";

    $result = &$dbconn->Execute($sql);

    if (!$result) {
        return;
    } else {
        $id = $dbconn->PO_Insert_ID($xartable['comments'], 'xar_cid');
        // CHECKME: find some cleaner way to update the page cache if necessary
        if (function_exists('xarPageFlushCached') &&
            xarModGetVar('xarcachemanager','FlushOnNewComment')) {
            $modinfo = xarModGetInfo($modid);
            // this may not be agressive enough flushing for all sites
            // we could flush "$modinfo[name]-" to remove all output cache associated with a module
            xarPageFlushCached("$modinfo[name]-user-display-");
        }
        return $id;
    }
}

?>
