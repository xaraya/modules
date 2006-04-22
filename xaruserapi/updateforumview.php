<?php
/**
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
 * @author John Cox
 * @author Jo dalle Nogare
 * @param $args['fid'] the forum id
 * @param $args['move'] 'positive' or 'negative'
 * @param $args['topics'] how many topics were added or removed
 * @param $args['replies'] how many replies were added or removed
 * @param $args['fposter'] userid of the last poster
 * @param $args['tid'] the last topic id
 * @param $args['ttitle'] the last topic title
 * @param $args['treplies'] the number of replies to the last topic
 * @param $args['fpostid'] last post time (for sync)
 * @param $args['ftopics'] total number of topics (for sync)
 * @param $args['fposts'] total number of posts (for sync)
 */
function xarbb_userapi_updateforumview($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (empty($fid)) {
        $msg = xarML('Invalid parameter count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xbbforumstable = $xartable['xbbforums'];
    if (isset($fpostid) && is_numeric($fpostid)) {
        $time = $fpostid;
    } else {
        $time = time();
    }

    $options = array();
    $options['tid'] = $tid;
    $options['ttitle'] = $ttitle;
    $options['treplies'] = (empty($treplies) ? 0 : $treplies);
    $options = serialize($options);

    // TODO: shouldn't xar_fpostid contain the last post id someday ?

    // Update the forum
    $query = "UPDATE $xbbforumstable SET xar_fpostid = ?, xar_foptions = ?,";

    if (!empty($topics) && is_numeric($topics)) {
        if ($move == 'positive') {
            $query .= " xar_ftopics = (xar_ftopics + $topics),";
        } else {
            $query .= " xar_ftopics = (xar_ftopics - $topics),";
        }
    } elseif (isset($ftopics) && is_numeric($ftopics)) { // for sync
        $query .= " xar_ftopics = $ftopics,";
    }

    if (!empty($replies) && is_numeric($replies)) {
        if ($move == 'positive') {
            $query .= " xar_fposts = (xar_fposts + $replies),";
        } else {
            $query .= " xar_fposts = (xar_fposts - $replies),";
        }
    } elseif (isset($fposts) && is_numeric($fposts)) { // for sync
        $query .= " xar_fposts = $fposts,";
    }

    $query .= "   xar_fposter   = ?
            WHERE xar_fid       = ?";
    $result =& $dbconn->Execute($query, array($time, $options, $fposter, $fid));
    if (!$result) return;

    // Let the calling process know that we have finished successfully
    return true;
}

?>