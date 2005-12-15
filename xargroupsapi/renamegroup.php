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
function xproject_groupsapi_renamegroup($args)
{
    extract($args);

    if((!isset($gid)) || (!isset($gname))) {
    xarSessionSetVar('errormsg', _MODARGSERROR);
    return false;
    }
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $groupstable = $xartable['groups'];

    // Get details on current group
    $query = "SELECT xar_name
              FROM $groupstable
              WHERE xar_gid=".xarVarPrepForStore($gid)."";
    $result = $dbconn->Execute($query);

    if ($result->EOF) {
        xarSessionSetVar('errormsg', 'No such group ID '.$gid.'');
    return false;
    }
    list($oldgname) = $result->fields;
    $result->Close();

    if (!xarSecAuthAction(0, 'Groups::', "$oldgname::$gid", ACCESS_EDIT)) {
        xarSessionSetVar('errormsg', _GROUPSEDITNOAUTH);
        return false;
    }
    $query = "UPDATE $groupstable
              SET xar_name=\"$gname\"
              WHERE xar_gid=".xarVarPrepForStore($gid)."";
    $dbconn->Execute($query);

    return true;
}

/*
 * viewgroup - view users in group
 * @param $args['gid'] group id
 * @return $users array containing uname, uid
 */
?>