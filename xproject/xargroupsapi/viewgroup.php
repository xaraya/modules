<?php

function xproject_groupsapi_viewgroup($args)
{
//    if (!xarModAPILoad('groups', 'user')) {
	    $users = xarModAPIFunc('xproject','groups','get');
//    } else {
//	    $users = xarModAPIFunc('groups','user','get');
//	}

    return $users;
}

/*
 * deleteuser - delete a user from a group
 * @param $args['gid'] group id
 * @param $args['uid'] user id
 * @return true on success, false on failure
 */
?>