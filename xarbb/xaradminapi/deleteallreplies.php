<?php
/**
 * File: $Id$
 * 
 * Delete topic replies for a given topic
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
        $msg = xarML('Invalid Parameter Count in #(1), #(2), #(3)', 'admin', 'deleteallreplies', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // get topic id
    $topic = xarModAPIFunc('xarbb','user','gettopic',array('tid' => $tid));

    if (!$topic){
        $msg = xarML('Could not get topic in #(1), #(2), #(3)', 'admin', 'deleteallreplies', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if(!xarSecurityCheck('ModxarBB',1,'Forum',$topic['catid'].':'.$topic['fid'])) return;

    $comments = xarModAPIFunc('comments', 'user', 'get_multiple', array('modid' => xarModGetIdFromName('xarbb'), 'objectid' => $tid));
    
    if (!isset($comments) || !is_array($comments)){
        $msg = xarML('Could not get comments in #(1), #(2), #(3)', 'admin', 'deleteallreplies', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if(count($comments) > 0)	{
    	xarModAPIFunc('comments', 'admin', 'delete_object_nodes', array('modid' => xarModGetIdFromName('xarbb'), 'objectid' => $tid));
	}
    return true;
}
?>
