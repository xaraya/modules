<?php

/**
 * Delete topic replies for a given topic
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
 * @param $args['tid'] Topic id
 * @returns bool
 * @return true on success, false on failure
 */

function xarbb_adminapi_deleteallreplies($args)
{
    extract($args);

    // Argument check
    if (!isset($tid))  {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Get topic id
    $topic = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));

    if (!$topic) {
        $msg = xarML('Could not get topic');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('ModxarBB', 1, 'Forum', $topic['catid'] . ':' . $topic['fid'])) return;

    xarModAPIFunc('comments', 'admin', 'delete_object_nodes',
        array(
            'modid' => xarModGetIdFromName('xarbb'),
            'itemtype' => $topic['fid'],
            'objectid' => $tid
        )
    );

    return true;
}

?>