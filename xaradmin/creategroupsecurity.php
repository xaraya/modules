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
    Adds a group security level to an item

    @return boolean true on success otherwise false
*/
function security_admin_creategroupsecurity($args)
{
    xarVarFetch('modid',    'id', $modid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype', 'id', $itemtype, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('itemid',   'id', $itemid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('group',    'id', $group, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('returnurl','str',$returnUrl, '', XARVAR_NOT_REQUIRED);

    extract($args);

    xarModAPILoad('security', 'user');

    // Set the default
    $level = SECURITY_READ;

    // Get DB conn ready
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['security_group_levels'];

    $query = "INSERT INTO $table (xar_modid, xar_itemtype, xar_itemid, xar_gid, xar_level)
        VALUES ( ?, ?, ?, ?, ? )
    ";
    $bindvars = array( $modid, $itemtype, $itemid, $group, $level );

    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ) return false;

    xarResponseRedirect($returnUrl);

    return true;
}
?>