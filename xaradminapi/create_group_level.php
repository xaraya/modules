<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
function security_adminapi_create_group_level($args)
{
    extract($args);

    if( !xarModAPILoad('security', 'user') ){ return false; }

    // Get DB conn ready
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $securityRolesTable = $xartable['security_roles'];

    $query = "INSERT INTO $securityRolesTable "
        . "(modid, itemtype, itemid, uid, xoverview, xread, xcomment, xwrite, xmanage, xadmin)"
        . "VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
    $bindvars = array(
        isset($modid)      ? $modid    : 0
        , isset($itemtype) ? $itemtype : 0
        , isset($itemid)   ? $itemid   : 0
        , isset($role_id)  ? $role_id  : 0
        , isset($level['overview'])? 1 : 0
        , isset($level['read'])    ? 1 : 0
        , isset($level['comment']) ? 1 : 0
        , isset($level['write'])   ? 1 : 0
        , isset($level['manage'])  ? 1 : 0
        , isset($level['admin'])   ? 1 : 0
    );

    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ) return false;

    return true;
}
?>