<?php

/**
 * File: $Id$
 *
 * contains the user funtions used for interacting with
 * the database as well as setting user options, etc.
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @author Carl P. Corliss <rabbitt@xaraya.com>
*/

/**
 * Comments API
 * @package Xaraya
 * @subpackage Comments_API
 */

include_once('modules/comments/xarincludes/defines.php');

/**
 * Activate the specified comment
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer     $comment_id     id of the comment to lookup
 * @returns  bool        returns true on success, throws an exception and returns false otherwise
 */
function comments_userapi_activate($comment_id) {
    if (empty($comment_id)) {
        $msg = xarML('Missing or Invalid parameter \'comment_id\'!!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // First grab the objectid and the modid so we can
    // then find the root node.
    $sql = "UPDATE $xartable[comments]
            SET xar_status='" . _COM_STATUS_ON."'
            WHERE xar_cid='$comment_cid'";

    $result =& $dbconn->Execute($sql);

    if (!$result)
        return;
}


/**
 * Activate the specified comment
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer     $comment_id     id of the comment to lookup
 * @returns  bool      returns true on success, throws an exception and returns false otherwise
 */
function comments_userapi_deactivate($comment_id) {
    if (empty($comment_id)) {
        $msg = xarML('Missing or Invalid parameter \'comment_id\'!!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // First grab the objectid and the modid so we can
    // then find the root node.
    $sql = "UPDATE $xartable[comments]
            SET xar_status='"._COM_STATUS_OFF."'
            WHERE xar_cid='$comment_cid'";

    $result =& $dbconn->Execute($sql);

    if (!$result)
        return;
}

/**
 * Grab the id, left and right values for the
 * root node of a particular comment.
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer     $comment_id     id of the comment to lookup
 * @returns  array an array containing the left and right values or an
 *                 empty array if the comment_id specified doesn't exist
 */
function comments_userapi_get_node_root($args) {
    extract ($args);

    if (!isset($objectid) || empty($objectid)) {
        $msg = xarML('Missing or Invalid parameter \'objectidj\'!!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($modid) || empty($modid)) {
        $msg = xarML('Missing or Invalid parameter \'modidj\'!!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $ctable = &$xartable['comments_column'];
    // grab the root node's id, left and right values
    // based on the objectid/modid pair
    $sql = "SELECT  $ctable[cid], $ctable[left], $ctable[right]
              FROM  $xartable[comments]
             WHERE  $ctable[modid]='$modid'
               AND  $ctable[objectid]='$objectid'
               AND  $ctable[status]='"._COM_STATUS_ROOT_NODE."'";

    $result =& $dbconn->Execute($sql);

    if(!$result)
        return;

    $count=$result->RecordCount();

    assert($count==1 | $count==0);

    if (!$result->EOF) {
        $node = $result->GetRowAssoc(false);
    } else {
        $node = array();
    }
    $result->Close();

    return $node;
}

/**
 * Grab the highest 'right' value for the specified modid/objectid pair
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   private
 * @returns   integer   the highest 'right' value for the specified modid/objectid pair or zero if it couldn't find one
 */
function comments_userapi_get_object_maxright( $args ) {

    extract ($args);

    if (!isset($objectid) || !isset($modid)) {
        // TODO:  raise exception
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $ctable = &$xartable['comments_column'];

    // grab the root node's id, left and right values
    // based on the objectid/modid pair
    $sql = "SELECT  MAX($ctable[right]) as max_right
              FROM  $xartable[comments]
             WHERE  $ctable[objectid] = $objectid
               AND  $ctable[modid] = $modid";

    $result =& $dbconn->Execute($sql);

    if (!$result)
        return;

    if (!$result->EOF) {
        $node = $result->GetRowAssoc(false);
    } else {
        $node['max_right'] = 0;
    }
    $result->Close();

    return $node['max_right'];
}

/**
 * Grab the highest 'right' value for the whole comments table
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   private
 * @returns   integer   the highest 'right' value for the table or zero if it couldn't find one
 */
function comments_userapi_get_table_maxright( ) {

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $ctable = &$xartable['comments_column'];


    // grab the root node's id, left and right values
    // based on the objectid/modid pair
    $sql = "SELECT  MAX($ctable[right]) as max_right
              FROM  $xartable[comments]";

    $result =& $dbconn->Execute($sql);

    if (!$result)
        return;

    if (!$result->EOF) {
        $node = $result->GetRowAssoc(false);
    } else {
        $node['max_right'] = 0;
    }
    $result->Close();

    return $node['max_right'];
}

/**
 * Grab the left and right values for a particular node
 * (aka comment) in the database
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer     $comment_id     id of the comment to lookup
 * @returns  array an array containing the left and right values or an
 *           empty array if the comment_id specified doesn't exist
 */
function comments_userapi_get_node_lrvalues($comment_id) {

    if (empty($comment_id)) {
        // TODO: raise exception
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $ctable = &$xartable['comments_column'];

    $sql = "SELECT  $ctable[left], $ctable[right]
              FROM  $xartable[comments]
             WHERE  $ctable[cid]='$comment_id'";

    $result =& $dbconn->Execute($sql);

    if(!$result)
        return;

    if (!$result->EOF) {
        $lrvalues = $result->GetRowAssoc(false);
    } else {
        $lrvalues = array();
    }

    $result->Close();

    return $lrvalues;
}

/**
 * Grab the left and right values for each object of a particular module
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer     $modid     id of the module to gather info on
 * @returns  array an array containing the left and right values or an
 *           empty array if the modid specified doesn't exist
 */
function comments_userapi_get_module_lrvalues($modid) {

    if (empty($modid)) {
        // TODO: raise exception
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $ctable = &$xartable['comments_column'];

    $sql = "SELECT  $ctable[objectid] AS xar_objectid,
                    MIN($ctable[left]) AS xar_left,
                    MAX($ctable[right]) AS xar_right
              FROM  $xartable[comments]
             WHERE  $ctable[modid]='$modid'
          GROUP BY  $ctable[objectid]";

    $result =& $dbconn->Execute($sql);

    if(!$result)
        return;

    if (!$result->EOF) {
        while (!$result->EOF) {
            $row = $result->GetRowAssoc(false);
            $lrvalues[] = $row;
            $result->MoveNext();
        }
    } else {
        $lrvalues = array();
    }

    $result->Close();

    return $lrvalues;
}

/**
 * Open a gap in the celko tree for inserting nodes
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   private
 * @param    integer    $startpoint    the point at wich the node will be inserted
 * @param    integer    $endpoint      end point for creating gap (used mostly for moving branches around)
 * @param    integer    $gapsize       the size of the gap to make (defaults to 2 for inserting a single node)
 * @returns  integer    number of affected rows or false [0] on error
 */
function comments_userapi_create_gap($startpoint, $gapsize = 2, $endpoint = NULL) {

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $sql_left  = "UPDATE $xartable[comments]
                     SET xar_left = (xar_left + $gapsize)
                   WHERE xar_left > $startpoint";

    $sql_right = "UPDATE $xartable[comments]
                     SET xar_right = (xar_right + $gapsize)
                   WHERE xar_right >= $startpoint";

    // if we have an endpoint, use it :)
    if (!empty($endpoint) && $endpoint !== NULL) {
        $sql_left   .= " AND xar_left <= $endpoint";
        $sql_right  .= " AND xar_right <= $endpoint";
    }

    $result1 =& $dbconn->Execute($sql_left);
    $result2 =& $dbconn->Execute($sql_right);

    if(!$result1 || !$result2) {
        return;
    }

    return $dbconn->Affected_Rows();
}

/**
 * Remove a gap in the celko tree
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   private
 * @param    integer    $startpoint    starting point for removing gap
 * @param    integer    $endpoint      end point for removing gap
 * @param    integer    $gapsize       the size of the gap to remove
 * @returns  integer    number of affected rows or false [0] on error
 */
function comments_userapi_remove_gap($startpoint, $endpoint, $gapsize) {

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $sql_left  = "UPDATE $xartable[comments]
                     SET xar_left = (xar_left - $gapsize)
                   WHERE xar_left > $startpoint";

    $sql_right = "UPDATE $xartable[comments]
                     SET xar_right = (xar_right - $gapsize)
                   WHERE xar_right >= $startpoint";

    // if we have an endpoint, use it :)
    if (!empty($endpoint) && $endpoint !== NULL) {
        $sql_left   .= " AND xar_left <= $endpoint";
        $sql_right  .= " AND xar_right <= $endpoint";
    }

    $result1 =& $dbconn->Execute($sql_left);
    $result2 =& $dbconn->Execute($sql_right);

    if(!$result1 || !$result2) {
        return;
    }

    return $dbconn->Affected_Rows();
}

/**
 * Creates a root node for the specified objectid/modid
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   private
 * @param    integer     $modid      the id of the module this is attached to
 * @param    string      $objectid     the particular item in the specified module that this is attached to
 * @returns   integer     the id of the node that was created so it can be used as a parent id
 */
function comments_userapi_add_rootnode($modid, $objectid) {

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $ctable = &$xartable['comments_column'];

    // grab the max right value
    $maxright = comments_userapi_get_table_maxright();

    // if we couldn't find a maxright then there isn't any
    // comments in the table yet so we start with maxright = 0 :)
    if (false == $maxright) {
        $maxright = 0;
    }

    $commenttable=$xartable['comments'];

    // Set left and right values;
    $left = $maxright + 1;
    $right = $maxright + 2;
    $cdate = time();

    // Get next ID in table.  For databases like MySQL, this value will
    // be zero but the auto_increment type on the column will create
    // the correct value.
    $nextId = $dbconn->GenId($commenttable);

    $sql = "INSERT INTO $xartable[comments] (
                                xar_cid,
                                xar_pid,
                                xar_text,
                                xar_title,
                                xar_author,
                                xar_left,
                                xar_right,
                                xar_status,
                                xar_objectid,
                                xar_modid,
                                xar_hostname,
                                xar_date
                                            )
                    VALUES (    $nextId,
                                0,
                                'This is for internal use and works only as a place holder. PLEASE do NOT delete this comment as it could have detrimental effects on the consistency of the comments table.',
                                'ROOT NODE - PLACEHOLDER. DO NOT DELETE!',
                                1,
                                $left,
                                $right,
                                "._COM_STATUS_ROOT_NODE.",
                                $objectid,
                                $modid,
                                '',
                                $cdate 
                            )";

    $result =& $dbconn->Execute($sql);

    if(!$result)
        return;

    // Return the cid of the created record just now.
    $cid = $dbconn->PO_Insert_ID($xartable['comments'], 'xar_cid');

    return $cid;
}

/**
 * Get a single comment.
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer    $args['cid']       the id of a comment
 * @returns  array   an array containing the sole comment that was requested
                     or an empty array if no comment found
 */
function comments_userapi_get_one($args) {

    extract($args);

    if(!isset($cid) || empty($cid)) {
        $msg = xarML('Missing or Invalid arguement [#(1)] for #(2) function #(3) in module #(4)',
                                 'cid','userapi','get_one','comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException(__FILE__.' ('.__LINE__.'):  '.$msg));
        return false;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

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
                    $ctable[postanon] AS xar_postanon,
                    $ctable[objectid] AS xar_objectid
              FROM  $xartable[comments]
             WHERE  $ctable[cid]='$cid'
               AND  $ctable[status]='"._COM_STATUS_ON."'";

    $result =& $dbconn->Execute($sql);
    if(!$result) return;

    // if we have nothing to return
    // we return nothing ;) duh? lol
    if ($result->EOF) {
        return array();
    }

    if (!xarModLoad('comments','renderer')) {
        $msg = xarML('Unable to load #(1) #(2) - unable to trim excess depth','comments','renderer');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNABLE_TO_LOAD', new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return;
    }

    // zip through the list of results and
    // add it to the array we will return
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        $row['xar_date'] = strftime("%B %d, %Y %I:%M %p",$row['xar_datetime']);
        $row['xar_author'] = xarUserGetVar('name',$row['xar_author']);
        comments_renderer_wrap_words($row['xar_text'],80);
        $commentlist[] = $row;
        $result->MoveNext();
    }

    $result->Close();

    if (!comments_renderer_array_markdepths_bypid($commentlist)) {
        // TODO: raise exception -- couldn't figure out depths for nodes
    }



    return $commentlist;
}

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
 * @param integer    $objectid  (optional) the id of the item that these nodes belong to
 * @param integer    $cid       (optional) the id of a comment
 * @param integer    $status    (optional) only pull comments with this status
 * @param integer    $author    (optional) only pull comments by this author
 * @returns array     an array of comments or an empty array if no comments
 *                   found for the particular modid/objectid pair, or raise an
 *                   exception and return false.
 */
function comments_userapi_get_multiple($args) {
    extract($args);

    // $modid, $objectid, $depth=_COM_MAX_DEPTH, $cid=0
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
        $nodelr = comments_userapi_get_node_lrvalues($cid);
    }

    if (!isset($status) || empty($status)) {
        $status = _COM_STATUS_ON;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

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

    $result =& $dbconn->Execute($sql);
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
        $row['xar_date'] = strftime("%B %d, %Y %I:%M %p",$row['xar_datetime']);
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

/**
 * Get the number of comments for a module item
 *
 * @author mikespub
 * @access public
 * @param integer    $modid     the id of the module that these nodes belong to
 * @param integer    $objectid    the id of the item that these nodes belong to
 * @returns integer  the number of comments for the particular modid/objectid pair,
 *                   or raise an exception and return false.
 */
function comments_userapi_get_count($args) {
    extract($args);
    // $modid, $objectid

    if ( !isset($modid) || empty($modid) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'modid', 'userapi', 'get_count', 'comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }


    if ( !isset($objectid) || empty($objectid) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'objectid', 'userapi', 'get_count', 'comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $ctable = &$xartable['comments_column'];

    $sql = "SELECT  COUNT($ctable[cid]) as numitems
              FROM  $xartable[comments]
             WHERE  ($ctable[objectid]='$objectid' AND $ctable[modid]='$modid')
               AND  $ctable[status]='"._COM_STATUS_ON."'";

    $result =& $dbconn->Execute($sql);
    if (!$result)
        return;

    if ($result->EOF) {
        return 0;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

/**
 * Get the number of comments for a module based on the author
 *
 * @author mikespub
 * @access public
 * @param integer    $modid     the id of the module that these nodes belong to
 * @param integer    $author      the id of the author you want to count comments for
 * @param integer    $status    (optional) the status of the comments to tally up
 * @returns integer  the number of comments for the particular modid/objectid pair,
 *                   or raise an exception and return false.
 */
function comments_userapi_get_author_count($args) {
    extract($args);
    // $modid, $author

    if ( !isset($modid) || empty($modid) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'modid', 'userapi', 'get_count', 'comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }


    if ( !isset($author) || empty($author) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'author', 'userapi', 'get_count', 'comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    if (!isset($status) || empty($status)) {
        $status = _COM_STATUS_ON;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $ctable = &$xartable['comments_column'];

    $sql = "SELECT  COUNT($ctable[cid]) as numitems
              FROM  $xartable[comments]
             WHERE  ($ctable[author]='$author' AND $ctable[modid]='$modid')
               AND  $ctable[status]='$status'";

    $result =& $dbconn->Execute($sql);
    if (!$result)
        return;

    if ($result->EOF) {
        return 0;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

/**
 * Get the number of comments for a list of module items
 *
 * @author mikespub
 * @access public
 * @param integer    $modid     the id of the module that these nodes belong to
 * @param array    $objectids    the list of ids of the items that these nodes belong to
 * @returns array  the number of comments for the particular modid/objectids pairs,
 *                   or raise an exception and return false.
 */
function comments_userapi_get_countlist($args) {
    extract($args);
    // $modid, $objectids

    if ( !isset($modid) || empty($modid) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'modid', 'userapi', 'get_countlist', 'comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }


    if ( !isset($objectids) || !is_array($objectids) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'objectids', 'userapi', 'get_countlist', 'comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $ctable = &$xartable['comments_column'];

    $sql = "SELECT  $ctable[objectid], COUNT($ctable[cid]) as numitems
              FROM  $xartable[comments]
             WHERE  $ctable[modid]='$modid'
               AND  $ctable[objectid] IN ('" . join("', '",$objectids) . "')
               AND  $ctable[status]='"._COM_STATUS_ON."'
          GROUP BY  $ctable[objectid]";

    $result =& $dbconn->Execute($sql);
    if (!$result)
        return;

    $count = array();
    while (!$result->EOF) {
        list($id,$numitems) = $result->fields;
        $count[$id] = $numitems;
        $result->MoveNext();
    }
    $result->Close();

    return $count;
}

/**
 * Get the number of children comments for a particular comment id
 *
 * @author mikespub
 * @access public
 * @param integer    $cid       the comment id that we want to get a count of children for
 * @returns integer  the number of child comments for the particular comment id,
 *                   or raise an exception and return false.
 */
function comments_userapi_get_childcount($cid) {

    if ( !isset($cid) || empty($cid) ) {
        $msg = xarML('Invalid #(1) [#(2)] for #(3) function #(4)() in module #(5)',
                                 'cid', $cid, 'userapi', 'get_childcount', 'comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }


    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $ctable = &$xartable['comments_column'];

    $nodelr = comments_userapi_get_node_lrvalues($cid);

    $sql = "SELECT  COUNT($ctable[cid]) as numitems
              FROM  $xartable[comments]
             WHERE  $ctable[status]='"._COM_STATUS_ON."'
               AND  ($ctable[left] >= $nodelr[xar_left] AND $ctable[right] <= $nodelr[xar_right])";

    $result =& $dbconn->Execute($sql);
    if (!$result)
        return;

    if ($result->EOF) {
        return 0;
    }

    list($numitems) = $result->fields;

    $result->Close();

    // return total count - 1 ... the -1 is so we don't count the comment root.
    return ($numitems - 1);
}

/**
 * Get the number of children comments for a list of comment ids
 *
 * @author mikespub
 * @access public
 * @param integer  $left the left limit for the list of comment ids
 * @param integer  $right the right limit for the list of comment ids
 * @returns array  the number of child comments for each comment id,
 *                   or raise an exception and return false.
 */
function comments_userapi_get_childcountlist($args) {

    extract($args);
    if ( !isset($left) || !is_numeric($left) ||
         !isset($right) || !is_numeric($right)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'left/right', 'userapi', 'get_childcountlist', 'comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $ctable = &$xartable['comments_column'];

    $sql = "SELECT P1.xar_cid, COUNT(P2.xar_cid) AS numitems
                  FROM $xartable[comments] AS P1,
                       $xartable[comments] AS P2
                 WHERE P2.xar_left
                    >= P1.xar_left
                   AND P2.xar_left
                    <= P1.xar_right
                   AND P1.xar_left >= $left
                   AND P1.xar_right <= $right
                   AND  P2.xar_status='"._COM_STATUS_ON."'
             GROUP BY P1.xar_cid";
/*
                   AND P1.xar_cid
                        IN (".join(', ',$cids).")
*/
    $result =& $dbconn->Execute($sql);
    if (!$result)
        return;

    if ($result->EOF) {
        return array();
    }

    $count = array();
    while (!$result->EOF) {
        list($cid,$numitems) = $result->fields;
        // return total count - 1 ... the -1 is so we don't count the comment root.
        $count[$cid] = $numitems - 1;
        $result->MoveNext();
    }

    $result->Close();

    return $count;
}

/**
 * Adds a comment to the database based on the objectid/modid pair
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer     $args['modid']      the module id
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

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $ctable = &$xartable['comments_column'];

    // parentid == zero then we need to find the root nodes
    // left and right values cuz we're adding the new comment
    // as a top level comment
    if ($pid == 0) {
        $root_lnr = comments_userapi_get_node_root(array('modid' => $modid, 'objectid' => $objectid));

        // ok, if the there was no root left and right values then
        // that means this is the first comment for this particular
        // modid/objectid combo -- so we need to create a dummy (root)
        // comment from which every other comment will branch from
        if (!count($root_lnr)) {
            $pid = comments_userapi_add_rootnode($modid, $objectid);
        } else {
            $pid = $root_lnr['xar_cid'];
        }
    }

    // pid should now always have a value
    assert($pid!=0 && !empty($pid));

    // grab the left and right values from the parent
    $parent_lnr = comments_userapi_get_node_lrvalues($pid);

    // there should be -at-least- one affected row -- if not
    // then raise an exception. btw, at the very least,
    // the 'right' value of the parent node would have been affected.
    if (!comments_userapi_create_gap($parent_lnr['xar_right'])) {
        // TODO: raise exception
        die ("Could not create gap for new comment insertion. I'm going to die a really horrible death now -- see you later!");
        return;
    }

    // the comment's date will be autoinserted by database
    $cdate    = time();
    $left     = $parent_lnr['xar_right'];
    $right    = $left + 1;
    $status   = xarModGetVar('comments','AuthorizeComments') ? _COM_STATUS_OFF : _COM_STATUS_ON;

    if (!isset($cid)) {
        $cid = $dbconn->GenId($xartable['comments']);
    }

    $sql = "INSERT INTO $xartable[comments]
                (xar_cid,
                 xar_modid,
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
        .xarVarPrepForStore($cid).",'"
        .xarVarPrepForStore($modid)."','"
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
        .xarVarPrepForStore($postanon)."')";

    $result = &$dbconn->Execute($sql);

    if (!$result) {
        return;
    } else {
        $cid = $dbconn->PO_Insert_ID($xartable['comments'], 'xar_cid');
        return $cid;
    }
}

/**
 * Acquire a list of objectid's associated with a
 * particular Module ID in the comments table
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  private
 * @param   integer     $modid      the id of the module that the objectids are associated with
 * @returns array A list of objectid's
 */
function comments_userapi_get_object_list( $modid ) {

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $ctable = &$xartable['comments_column'];

	$sql     = "SELECT DISTINCT $ctable[objectid] AS pageid
                           FROM $xartable[comments]
                          WHERE $ctable[modid] = '$modid'";


    $result =& $dbconn->Execute($sql);
    if (!$result) return;

    // if it's an empty set, return array()
    if ($result->EOF) {
        return array();
    }

    // zip through the list of results and
    // create the return array
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        $ret[$row['pageid']]['pageid'] = $row['pageid'];
        $result->MoveNext();
    }
    $result->Close();

    return $ret;

}

/**
 * Modify a comment
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_userapi_modify($args) {

    extract($args);

    $msg = xarML('Missing or Invalid Parameters: ');;
    $error = FALSE;

    if (!isset($title)) {
        $msg .= xarMLbykey(' title ');
        $error = TRUE;
    }

    if (!isset($cid)) {
        $msg .= xarMLbykey(' cid ');
        $error = TRUE;
    }

    if (!isset($text)) {
        $msg .= xarMLbykey(' text ');
        $error = TRUE;
    }

    if (!isset($postanon)) {
        $msg .= xarMLbykey(' postanon ');
        $error = TRUE;
    }

    if ($error) {
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    $forwarded = xarServerGetVar('HTTP_X_FORWARDED_FOR');
    if (!empty($forwarded)) {
        $hostname = preg_replace('/,.*/', '', $forwarded);
    } else {
        $hostname = xarServerGetVar('REMOTE_ADDR');
    }

    $modified_date = strftime("%B %d, %Y %I:%M %p",time());

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $text .= "\n<br />\n<br />\n" . xarML('[Modified by: #(1) (#(2)) on #(3)]', xarUserGetVar('name'),
                                                                          xarUserGetVar('uname'),
                                                                          $modified_date);

    $sql =  "UPDATE $xartable[comments]
                SET xar_title    = '". xarVarPrepForStore($title) ."',
                    xar_text  = '". xarVarPrepForStore($text) ."',
                    xar_anonpost = '". xarVarPrepForStore($postanon) ."'
              WHERE xar_cid='$cid'";

    $result = &$dbconn->Execute($sql);

    if (!$result) {
        return;
    }

}

/**
 * Searches all active comments based on a set criteria
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_userapi_search($args) {

    if (empty($args) || count($args) < 1) {
        return;
    }

    extract($args);

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $ctable = &$xartable['comments_column'];
    $where = '';


    // initialize the commentlist array
    $commentlist = array();

    $sql = "SELECT  $ctable[title] AS xar_title,
                    $ctable[cdate] AS xar_date,
                    $ctable[author] AS xar_author,
                    $ctable[cid] AS xar_cid,
                    $ctable[pid] AS xar_pid,
                    $ctable[postanon] AS xar_postanon,
                    $ctable[modid]  AS xar_modid,
                    $ctable[objectid] as xar_objectid
              FROM  $xartable[comments]
             WHERE  $ctable[status]='"._COM_STATUS_ON."'
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
        $row['xar_date'] = strftime("%B %d, %Y %I:%M %p",$row['xar_date']);
        $row['xar_author'] = xarUserGetVar('name',$row['xar_author']);
        $commentlist[] = $row;
        $result->MoveNext();
    }
    $result->Close();

    if (!xarModLoad('comments','renderer')) {
        $msg = xarML('Unable to load #(1) #(2)','comments','renderer');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNABLE_TO_LOAD', new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return;
    }

    if (!comments_renderer_array_markdepths_bypid($commentlist)) {
        $msg = xarML('Unable to create depth by pid');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'SYSTEM_ERROR', new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return;
    }

    comments_renderer_array_sort($commentlist, _COM_SORTBY_TOPIC, _COM_SORT_ASC);
    $commentlist = comments_renderer_array_prune_excessdepth(array('array_list'  => $commentlist,
                                                                   'cutoff'      => _COM_MAX_DEPTH));
    comments_renderer_array_maptree($commentlist);

    return $commentlist;

}

/**
 * Grabs the list of viewing options in the following order of precedence:
 * 1. POST/GET
 * 2. User Settings (if user is logged in)
 * 3. Module Defaults
 * 4. internal defaults
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access public
 * @returns array list of viewing options (depth, render style, order, and sortby)
 */
function comments_userapi_getoptions() {

    $depth      = xarVarCleanFromInput('depth');
    $render     = xarVarCleanFromInput('render');
    $order      = xarVarCleanFromInput('order');
    $sortby     = xarVarCleanFromInput('sortby');

    // if one of the settings configured, the all should be.
    // Order of precedence for determining which
    // settings to use.  (User_Defined is (obviously)
    // dependant on the user being logged in.):
    // Get/Post->[user_defined->]admin_defined

    if (isset($depth)) {
        if ($depth == 0) {
            $settings['depth'] = 1;
        } else {
            $settings['depth'] = $depth;
        }
    } else {
        if (xarUserIsLoggedIn()) {
            // Grab user's depth setting.
            $settings['depth'] = xarModGetUserVar('comments','depth');
        } else {
            $settings['depth'] = xarModGetVar('comments','depth');
        }
    }

    if (isset($render) && !empty($render)) {
        $settings['render'] = $render;
    } else {
        if (xarUserIsLoggedIn()) {
            // Grab user's depth setting.
            $settings['render'] = xarModGetUserVar('comments','render');
        } else {
            $settings['render'] = xarModGetVar('comments','render');
        }
    }

    if (isset($order) && !empty($order)) {
        $settings['order'] = $order;
    } else {
        if (xarUserIsLoggedIn()) {
            // Grab user's depth setting.
            $settings['order'] = xarModGetUserVar('comments','order');
        } else {
            $settings['order'] = xarModGetVar('comments','order');
        }
    }

    if (isset($sortby) && !empty($sortby)) {
        $settings['sortby'] = $sortby;
    } else {
        if (xarUserIsLoggedIn()) {
            // Grab user's depth setting.
            $settings['sortby'] = xarModGetUserVar('comments','sortby');
        } else {
            $settings['sortby'] = xarModGetVar('comments','sortby');
        }
    }

    if (!isset($settings['depth']) || $settings['depth'] > (_COM_MAX_DEPTH - 1)) {
        $settings['depth'] = (_COM_MAX_DEPTH - 1);
    }

    if (empty($settings['render'])) {
        $settings['render'] = _COM_VIEW_THREADED;
    }

    if (empty($settings['order'])) {
        $settings['order'] = _COM_SORT_ASC;
    }

    if (empty($settings['sortby'])) {
        $settings['sortby'] = _COM_SORTBY_THREAD;
    }

    return $settings;
}

/**
 * Set a user's viewing options
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_userapi_setoptions($args) {

    extract($args);

    if (isset($depth)) {
        if ($depth == 0) {
            $depth = 1;
        }
        if ($depth > (_COM_MAX_DEPTH - 1)) {
            $depth = (_COM_MAX_DEPTH - 1);
        }
    } else {
        $depth = xarModGetVar('comments','depth');
    }

    if (empty($render)) {
        $render = xarModGetVar('comments','render');
    }

    if (empty($order)) {
        $order = xarModGetVar('comments','order');
    }

    if (empty($sortby)) {
        $sortby = xarModGetVar('comments','sortby');
    }

    if (xarUserIsLoggedIn()) {
            // Grab user's depth setting.
            xarModSetUserVar('comments','depth',$depth);
            xarModSetUserVar('comments','render',$render);
            xarModSetUserVar('comments','sortby',$sortby);
            xarModSetUserVar('comments','order',$order);
    }

    return true;

}

?>
