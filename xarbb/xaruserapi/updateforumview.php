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
 * @param $args['fid'] the ID of the link
 * @param $args['fname'] the new keyword of the link
 * @param $args['fdesc'] the new title of the link
 */
function xarbb_userapi_updateforumview($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($fid)) {
        $msg = xarML('Invalid Parameter Count in #(1)api_#(2) in module #(3)', 'user', 'updateforumsview', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }


    if (isset($fid)){
        // The user API function is called
        $link = xarModAPIFunc('xarbb',
                              'user',
                              'getforum',
                              array('fid' => $fid));

        if ($link == false) {
            $msg = xarML('No Such Forum Present', 'xarbb');
            xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
            return;
        }
    }

    // Let's just tell ourselves what the number should be
    // Little more sane than the other way.
    if ((isset($replies)) && (!isset($topics))){
        // First move positive or negative
        if ($move == 'positive'){
            $fposts = $link['fposts'] + $replies;
            $ftopics = $link['ftopics'];
        } else {
            $fposts = $link['fposts'] - $replies;
            $ftopics = $link['ftopics'];
        }
    }
    // Let's just tell ourselves what the number should be
    if ((isset($topics)) && (!isset($replies))){
        // First move positive or negative
        if ($move == 'positive'){
            $ftopics = $link['ftopics'] + $topics;
            $fposts = $link['fposts'];
        } else {
            $ftopics = $link['ftopics'] - $topics;
            $fposts = $link['fposts'];
        }
    }

    // Let's just tell ourselves what the number should be
    if ((isset($topics)) && (isset($replies))){
        // First move positive or negative
        if ($move == 'positive'){
            $ftopics = $link['ftopics'] + $topics;
            $fposts = $link['fposts'] + $replies;
        } else {
            $ftopics = $link['ftopics'] - $topics;
            $fposts = $link['fposts'] - $replies;
        }
    }

    //oof, i needs to clean this up.  logic is getting murky around here.
    /*
    if ((empty($reply)) AND (empty($deletetopic)) AND (empty($deletereply))){
        $ftopics = $link['ftopics'] + 1;
        $fposts = $link['fposts'] +1;
    } elseif (!empty($reply)) {
        $ftopics = $link['ftopics'];
        $fposts = $link['fposts'] +1;
    } elseif (!empty($deletetopic)){
        $ftopics = $link['ftopics'] - 1;
        $fposts = $link['fposts'] - 1;
    } elseif (!empty($deletereply)){
        $ftopics = $link['ftopics'];
        $fposts = $link['fposts'] - 1;
    }
    */

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xbbforumstable = $xartable['xbbforums'];
    $time = time();

    // Update the forum
    $query = "UPDATE $xbbforumstable
            SET xar_ftopics     = $ftopics,
                xar_fpostid     = $time,
                xar_fposts      = $fposts,
                xar_fposter     = $fposter
            WHERE xar_fid       = " . xarVarPrepForStore($fid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let the calling process know that we have finished successfully
    return true;
}
?>