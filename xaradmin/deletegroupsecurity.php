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
    Removes group security level from an item

    @return boolean true on success otherwise false
*/
function security_admin_deletegroupsecurity($args)
{
    extract($args);

    if( !xarVarFetch('modid',    'id', $modid,     0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('itemtype', 'id', $itemtype,  0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('itemid',   'id', $itemid,    0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('group',    'id', $role_id,   0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('returnurl','str',$returnUrl, '', XARVAR_NOT_REQUIRED) ){ return false; }

    // Get DB conn ready
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $secRolesTable = $xartable['security_roles'];

    $query = "DELETE FROM $secRolesTable ";
    $where[] = " modid = ? ";
    $bindvars[] = isset($modid) ? $modid : 0;
    $where[] = " itemtype = ? ";
    $bindvars[] = isset($itemtype) ? $itemtype : 0;
    $where[] = " itemid = ? ";
    $bindvars[] = isset($itemtype) ? $itemid : 0;
    $where[] = " uid = ? ";
    $bindvars[] = $role_id;

    $query .= " WHERE " . join(" AND ", $where);

    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ) return false;

    xarResponseRedirect($returnUrl);

    return true;
}
?>