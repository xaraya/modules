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
function xproject_groupsapi_get($args)
{
    extract($args);

    if (!isset($gid)) {
        xarSessionSetVar('errormsg', _MODARGSERROR2);
        return false;
    }

    if (!xarSecurityCheck('ReadXProject', 0, 'Group', "All:All:All")) {// TODO: add $gid
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $groupstable = $xartable['groups'];
    $groupscolumn = &$xartable['groups_column'];

    $query = "SELECT xar_gid,
                     xar_name
              FROM $groupstable
            WHERE xar_gid = ?";
    $result = &$dbconn->Execute($query,array($gid));

    if (!$result) return;
    /* Check for no rows found, and if so, close the result set and return an exception */
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    // List groupid and name
    list($gid, $gname) = $result->fields;

    $result->Close();
    // Get the group members
    $groupmembers = xarModAPIFunc('xproject','groups','getmembers',array('gid' => $gid));
    $memberlist = array();
    foreach($groupmembers as $member) {
        $memberlist[] = $member['uid'];
    }
    // Members get read access
    if(in_array(xarSessionGetVar('uid'),$memberlist) || (xarSecurityCheck('ReadXProject', 0, 'Group', "All:All:All"))) {
        $group = array('gid'	=> $gid,
                       'gname'  => $gname);
    }
    return $group;
}

?>