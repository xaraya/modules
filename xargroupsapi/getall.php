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
function xproject_groupsapi_getall()
{
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $groupstable = $xartable['groups'];

    if (!xarSecAuthAction(0, 'Groups::', "::", ACCESS_OVERVIEW)) {
    xarSessionSetVar('errormsg', _GROUPSNOAUTH);
        return false;
    }

    $groups = array();

    // Get and display current groups
    $query = "SELECT xar_gid,
                     xar_name
              FROM $groupstable
              ORDER BY xar_name";
    $result = $dbconn->Execute($query);

    if($dbconn->ErrorNo() !=0) {
        xarSessionSetVar('errormsg', 'Error getting groups.');
        return false;
    }

    for(; !$result->EOF; $result->MoveNext()) {
        list($gid, $name) = $result->fields;

        $groupmembers = xarModAPIFunc('xproject','groups','getmembers',array('gid' => $gid));
        $memberlist = array();
        foreach($groupmembers as $member) $memberlist[] = $member['uid'];

        if(in_array(xarSessionGetVar('uid'),$memberlist) ||
            (xarSecAuthAction(0, 'Groups::', "$name::$gid", ACCESS_OVERVIEW))) {
            $groups[] = array('gid'  => $gid,
                              'name' => $name);
        }
    }

    $result->Close();

    return $groups;
}


/*
 * viewgroup - view users in group
 * @param $args['gid'] group id
 * @return $users array containing uname, uid
 */
?>