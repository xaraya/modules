<?php
/**
 * File: $Id$
 * 
 * Update a topic view
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_userapi_updatetopicsview($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($tid)) {
        $msg = xarML('Invalid Parameter Count',
                    '', 'admin', 'update', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('xarbb',
                          'user',
                          'gettopic',
                          array('tid' => $tid));

    if ($link == false) {
        $msg = xarML('No Such Topic Present',
                    'xarbb');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return;
    }

    // Security Check: needed? only called through this module and data inconsistency if fails (wrong number of reply posts,..)
    // if(!xarSecurityCheck('ReadxarBB')) return;

    //---------------------------------------------------------------
    // DO Update Stuff
    $treplies = xarModAPIFunc('comments','user','get_count',array(
        'modid' => xarModGetIdFromName('xarbb'),
        'objectid' => $tid
        ));

    // FIXME 0 replies - it's ok
    // if(!$treplies) return;

    $param = array(
        "tid" => $tid,
        "treplies" => $treplies,
        );

    if(isset($treplier)) {
        $param["treplier"] = $treplier;
        $param["time"] = time();
    }

    // We also want to call the hooks seperately so that the reply information
    // is supplied rather than the small amout of topic information that is known

    $param['nohooks'] = true;

    // Update the topic: call api func
    if(!xarModAPIFunc('xarbb','user','updatetopic',$param)) return;

    // Let the calling process know that we have finished successfully
    return true;
}
?>