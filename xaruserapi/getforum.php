<?php
/**
 * Get info for a forum
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
 * @poaram $args['lid'] id of link to get
 * @returns array
 * @return link array, or false on failure
 */
function xarbb_userapi_getforum($args)
{
    // Get all matching forums, limited to 2 for speed
    $forums = xarModAPIfunc(
        'xarbb', 'user', 'getallforums',
        array_merge($args, array('numitems' => 2))
    );

/*
// FIXME: forums may be assigned to several categories in the future, so xarbb should accept
          the fact that catid may contain several category ids (someday)
    if (count($forums) > 1) {
        $msg = xarML('Too many forums matched criteria');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST', new SystemException($msg));
        return;
    }
*/

    if (count($forums) == 0) {
        $msg = xarML('Selected forum does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST', new SystemException($msg));
        return;
    }

    $forum = reset($forums);

    return $forum;
}

?>
