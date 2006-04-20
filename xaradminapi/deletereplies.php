<?php

/**
 * Delete all replies
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * delete replies
 * @param $args['cids'] Array( IDs ) of the forum   or $args['cid'] ID
 * @returns bool
 * @return true on success, false on failure
 */

function xarbb_adminapi_deletereplies($args)
{ 
    extract($args);

    // Argument check
    if ((!isset($cids) || !is_array($cids) || count($cids) <= 0) && (!isset($cid) || !($cid > 0))) {
        $msg = xarML('Invalid parameter count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($cids)) $cids = Array($cid);

    // Security Check
    foreach($cids as $cid) {
        // for update topics view
        $comment = xarModAPIFunc('comments', 'user', 'get_one', array('cid' => $cid));
        if (empty($comment)) return;

        $tid = $comment[0]['xar_objectid'];

        $topic = xarModAPIFunc('xarbb','user','gettopic',array('tid' => $tid));
        if (empty($topic)) return;

        if (!xarSecurityCheck('ModxarBB', 1, 'Forum', $topic['catid'] . ':' . $topic['fid'])) continue;

        $pid = $comment[0]['xar_pid'];
        if (!xarModAPIFunc('comments', 'admin', 'delete_node', array('node' => $cid, 'pid' => $pid))) return;

        // update topics view, must do this here, because cids can contain different tids
        if (!xarModAPIFunc('xarbb', 'user', 'updatetopicsview', array('tid' => $tid))) return;
    }

    // Hooks should be called from comments module
    // Let the calling process know that we have finished successfully
    return true;
}

?>