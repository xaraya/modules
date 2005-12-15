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
/**
 * viewallgroups - generate all groups listing.
 * @param none
 * @return groups listing of available groups
 */
function xproject_groupsapi_viewallgroups()
{
    if (!xarModAPILoad('groups', 'user')) {
        $groups = xarModAPIFunc('xproject','groups','getall');
    } else {
        $groups = xarModAPIFunc('groups','user','getall');
    }

    return $groups;
}


/*
 * deletegroup - delete a group & info
 * @param $args['gid']
 * @return true on success, false otherwise
 */
?>