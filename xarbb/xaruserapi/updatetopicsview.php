<?php

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
    if(!$treplies)
    	return;

    $param = array(
    	"tid" => $tid,
        "treplies" => $treplies,
        );

    if(isset($treplier))             {
    	$param["treplier"] = $treplier;
        $param["time"] = date('Y-m-d G:i:s');
	}

    // Update the topic: call api func
    if(!xarModAPIFunc('xarbb','user','updatetopic',$param)) return;

    // Let the calling process know that we have finished successfully
    return true;
}

?>
