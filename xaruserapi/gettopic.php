<?php

/**
 * Get info for a specific forum topic
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
 * get a specific link
 * @params same as getalltopics()
 * @returns array
 * @return single topic array, or NULL on failure
 */

function xarbb_userapi_gettopic($args)
{
    // Get all matching topics, limited to 2 for speed
    $topics = xarModAPIfunc(
        'xarbb', 'user', 'getalltopics',
        array_merge($args, array('numitems' => 2))
    );

    if (count($topics) > 1) {
        $msg = xarML('Too many topics matched criteria');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST', new SystemException($msg));
        return;
    }

    if (count($topics) == 0) {
        $msg = xarML('Selected topic does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST', new SystemException($msg));
        return;
    }

    $topic = reset($topics);

    return $topic;
}

?>