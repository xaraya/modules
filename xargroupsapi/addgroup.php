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
function xproject_groupsapi_addgroup($args)
{
    extract($args);
    // TODO: Add gid to Security check
    if (!xarSecurityCheck('AdminXProject', 0, 'Group', "All:All:All")) {
        return;
    }

    if(!isset($gname) || (!is_string($gname))) {
        $invalid[] = 'gname';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'groups', 'addgroup', 'XProject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $groupstable = $xartable['groups'];

    // Confirm that this group does not already exist
    $query = "SELECT COUNT(*) FROM $groupstable
              WHERE xar_name =$gname";

    $result = $dbconn->Execute($query);

    list($count) = $result->fields;
    $result->Close();

    if ($count == 1) {
        xarSessionSetVar('errormsg', xarML('Group already exists'));
    return false;
    } else {
        $nextId = $dbconn->GenId($grouptable);
        $query = "INSERT INTO $groupstable
                  VALUES (?,?)";

    $bindvars = array($nextId, (string) $gname);
    $result = &$dbconn->Execute($query,$bindvars);

    return true;
    }
}

?>