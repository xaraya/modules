<?php
/**
 * File: $Id$
 * 
 * Update a forum view
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * update forum view
 * @param $args['fid'] the forum id
 * @param $args['move'] 'positive' or 'negative'
 * @param $args['topics'] how many topics were added or removed
 * @param $args['replies'] how many replies were added or removed
 * @param $args['fposter'] userid of the last poster
 */
function xarbb_userapi_updateforumview($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (empty($fid)) {
        $msg = xarML('Invalid Parameter Count in #(1)api_#(2) in module #(3)', 'user', 'updateforumsview', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

/*
    $forum = xarModAPIFunc('xarbb',
                           'user',
                           'getforum',
                           array('fid' => $fid));
    if (empty($forum)) {
        $msg = xarML('No Such Forum Present');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
*/

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xbbforumstable = $xartable['xbbforums'];
    $time = time();

// TODO: shouldn't xar_fpostid contain the last post id someday ?

    // Update the forum
    $query = "UPDATE $xbbforumstable
            SET xar_fpostid     = ?,";
    if (!empty($topics)) {
        if ($move == 'positive') {
            $query .= " xar_ftopics = (xar_ftopics + $topics),";
        } else {
            $query .= " xar_ftopics = (xar_ftopics - $topics),";
        }
    }
    if (!empty($replies)) {
        if ($move == 'positive') {
            $query .= " xar_fposts = (xar_fposts + $replies),";
        } else {
            $query .= " xar_fposts = (xar_fposts - $replies),";
        }
    }
    $query .= "   xar_fposter   = ?
            WHERE xar_fid       = ?";
    $result =& $dbconn->Execute($query, array($time, $fposter, $fid));
    if (!$result) return;
    // Let the calling process know that we have finished successfully
    return true;
}
?>
