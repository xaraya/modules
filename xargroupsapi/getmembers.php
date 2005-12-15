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
function xproject_groupsapi_getmembers($args)
{
    extract($args);

    // TODO: Add gid to Security check
    if (!xarSecurityCheck('ReadXProject', 0, 'Group', "All:All:All")) {
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $userstable = $xartable['users'];
    $groupmembership = $xartable['group_membership'];

    $users = array();
    // Get users in this group
    $query = "SELECT DISTINCT xar_uid
              FROM $groupmembership";

    if(isset($gid)) $query .= " WHERE xar_gid = $gid";
    elseif(isset($eid)) {
        $query .= " WHERE xar_gid = $eid";
        $exclude = " NOT";
    }

    $result = $dbconn->Execute($query);
    if (!$result->EOF) {
        for(;list($uid) = $result->fields;$result->MoveNext() ) {
            $uids[] = $uid;
        }
        $result->Close();
        $uidlist=implode(",", $uids);

        // Get names of users
        $query = "SELECT xar_uname,
                         xar_uid
                  FROM $userstable
                  WHERE xar_uid" . $exclude . " IN ($uidlist)
                  ORDER BY xar_uname";
        $result = $dbconn->Execute($query);

        while(list($uname, $uid) = $result->fields) {
            $result->MoveNext();
            $users[] = array('uname' => $uname,
                     'uid'   => $uid);
        }
        $result->Close();
    }
    return $users;
}

?>