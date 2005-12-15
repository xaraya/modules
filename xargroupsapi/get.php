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

    if (!xarSecAuthAction(0, 'groups::', "::", ACCESS_READ)) {
        xarSessionSetVar('errormsg', xarML('Not authorised to access Tasks module'));
        return false;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $groupstable = $xartable['groups'];
    $groupscolumn = &$xartable['groups_column'];

    $query = "SELECT xar_gid,
                     xar_name
              FROM $groupstable
            WHERE $groupscolumn[gid] = " . $gid;
    $result = $dbconn->Execute($query);

    if ($dbconn->ErrorNo() != 0) {
        xarSessionSetVar('errormsg', $query);
        return false;
    }

    if ($result->EOF) {
        xarSessionSetVar('errormsg', $query);
        return false;
    }

    list($gid, $gname) = $result->fields;

    $result->Close();

    $groupmembers = xarModAPIFunc('xproject','groups','getmembers',array('gid' => $gid));
    $memberlist = array();
    foreach($groupmembers as $member) $memberlist[] = $member['uid'];

    if(in_array(xarSessionGetVar('uid'),$memberlist)
        || (xarSecAuthAction(0, 'Groups::', "$gname::$gid", ACCESS_READ))) {
        $group = array('gid'	=> $gid,
                    'gname'		=> $gname);
    }


    return $group;
}

?>