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
function xproject_groupsapi_deletegroup($args)
{
    extract($args);

    if(!isset($gid)) {
    xarSessionSetVar('errormsg', _MODARGSERROR);
    return false;
    }
    if (!xarSecAuthAction(0, 'Groups::', "$gname::$gid", ACCESS_EDIT)) {
    xarSessionSetVar('errormsg', _GROUPSNOAUTH);
        return false;
    }
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $groupstable = $xartable['groups'];
    $groupmembership = $xartable['group_membership'];
    $groupperms = $xartable['group_perms'];

    // Delete permissions for the group
    $query = "DELETE FROM $groupperms
              WHERE xar_gid=".xarVarPrepForStore($gid)."";
    $dbconn->Execute($query);

    // Delete membership of the group
    $query = "DELETE FROM $groupmembership
              WHERE xar_gid=".xarVarPrepForStore($gid)."";
    $dbconn->Execute($query);

    // Delete the group itself
    $query = "DELETE FROM $groupstable
              WHERE xar_gid=".xarVarPrepForStore($gid)."";
    $dbconn->Execute($query);

    return true;
}

/*
 * renamegroup - rename a group
 * @param $args['gid'] group id
 * @param $args['gname'] group name
 * @return true on success, false on failure.
 */
?>