<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
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