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
/**
 * Delete security level(s) based on args passed in.
 *
 * @param array $args
 */
function security_adminapi_delete($args)
{
    extract($args);

    // At this point we must have a modid.
    if( empty($modid) ){ return false; }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['security'];
    $groupTable = $xartable['security_group_levels'];

    $sql = "DELETE FROM $table WHERE xar_modid = ? ";
    $bindvars = array($modid);
    $result = $dbconn->Execute($sql, $bindvars);
    if( !$result ) return false;

    $sql = "DELETE FROM $groupTable WHERE xar_modid = ? ";
    $bindvars = array($modid);
    $result = $dbconn->Execute($sql, $bindvars);
    if( !$result ) return false;

    return true;
}
?>