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

    xarVarFetch('modid',    'id', $modid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype', 'id', $itemtype, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('itemid',   'id', $itemid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('gid',      'id', $group, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('returnurl','str',$returnUrl, '', XARVAR_NOT_REQUIRED);

    // Get DB conn ready
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['security_group_levels'];

    $query = "DELETE FROM $table
    ";
    $where[] = " xar_modid = ? ";
    $bindvars[] = $modid;

    if($itemtype)
    {
        $where[] = " xar_itemtype = ? ";
        $bindvars[] = $itemtype;
    }
    if($itemid)
    {
        $where[] = " xar_itemid = ? ";
        $bindvars[] = $itemid;
    }
    if($group)
    {
        $where[] = " xar_gid = ? ";
        $bindvars[] = $group;
    }
    $query .= " WHERE " . join(" AND ", $where);

    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ) return false;

    xarResponseRedirect($returnUrl);

    return true;
}
?>