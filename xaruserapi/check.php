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
    Check to see if user has access to a xaraya item

    @param int $args['modid']
    @param int $args['itemtype']
    @param int $args['itemid']
    @param int $args['level']

    @return boolean
*/
function security_userapi_check($args)
{
    extract($args);

    if( xarSecurityCheck('AdminPanel', 0) ){ return true; }

    // Not really needed
    /*$cache_name = md5(serialize($args));
    if( xarVarIsCached('security', $cache_name) )
    {
        return xarVarGetCached('security', $cache_name);
    }*/


    // Make sure the need module API's are loaded
    xarModAPILoad('owner', 'user');

    // Get current user and groups
    $currentUserId = xarUserGetVar('uid');
    $groups = array();

    // Get Module Settings
    $settings = xarModAPIFunc('security', 'user', 'get_default_settings',
        array(
            'modid' => !empty($modid)?$modid:null,
            'itemtype' => !empty($itemtype)?$itemtype:null
        )
    );

    // Get DB conn ready
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $secTable = $xartable['security'];
    $secGroupLevelTable = $xartable['security_group_levels'];
    $ownerTable = $xartable['owner'];

    $bindvars = array();
    $where = array();
    $query = "
        SELECT xar_userlevel, xar_worldlevel,
               $secGroupLevelTable.xar_gid, $secGroupLevelTable.xar_level
        FROM $secTable
    ";

    if( !is_null($settings['owner']) and count($settings['owner']) == 3 )
    {
        $query .= "
            LEFT JOIN {$settings['owner']['table']} ON
            $secTable.xar_itemid = {$settings['owner']['primary_key']}
        ";
    }
    else
    {
        $query .= "
            LEFT JOIN $ownerTable ON
                $secTable.xar_modid    = $ownerTable.xar_modid  AND
                $secTable.xar_itemtype = $ownerTable.xar_itemtype AND
                $secTable.xar_itemid   = $ownerTable.xar_itemid
        ";
    }
    $query .= "
        LEFT JOIN $secGroupLevelTable ON
            $secTable.xar_modid    = $secGroupLevelTable.xar_modid AND
            $secTable.xar_itemtype = $secGroupLevelTable.xar_itemtype AND
            $secTable.xar_itemid   = $secGroupLevelTable.xar_itemid
    ";

    if( !empty($modid) )
    {
        $where[] = "$secTable.xar_modid = ?";
        $bindvars[] = (int)$modid;
    }
    if( !empty($itemtype) )
    {
        $where[] = "$secTable.xar_itemtype = ?";
        $bindvars[] = (int)$itemtype;
    }
    if( !empty($itemid) )
    {
        $where[] = "$secTable.xar_itemid = ?";
        $bindvars[] = (int)$itemid;
    }

    // User Check
    $currentUserId = (int)$currentUserId;
    $level = (int)$level;
    if( !is_null($settings['owner']) and count($settings['owner']) == 3 )
    {
        $secCheck[] = " ( $secTable.xar_userlevel & $level AND
            {$settings['owner']['table']}.{$settings['owner']['column']} = ? ) ";
        $bindvars[] = $currentUserId;
    }
    else
    {
        $secCheck[] = " ( $secTable.xar_userlevel & $level AND $ownerTable.xar_uid = ? ) ";
        $bindvars[] = $currentUserId;
    }

    //Check Groups
    // TODO: Maybe join on the roles members table for this prolly faster
    $roles = new xarRoles();
    $user = $roles->getRole($currentUserId);
    $parents = $user->getParents();
    foreach( $parents as $parent )
    {
        $secCheck[] = " ( $secGroupLevelTable.xar_gid = {$parent->uid} AND xar_level & $level ) ";
    }

    // Check for world
    $secCheck[] = " ( $secTable.xar_worldlevel & $level ) ";

    $where[] = " ( " . join(" OR ", $secCheck) . " ) ";

    if( count($where) > 0 )
    {
        $query .= ' WHERE ' . join(' AND ', $where);
    }

    //var_dump($query);
    $result = $dbconn->Execute($query, $bindvars);

    if( $result->EOF )
    {
        if( empty($hide_exception) )
        {
            $msg = "You do not have the proper security to perform this action!";
            xarErrorSet(XAR_USER_EXCEPTION, 'NO_PRIVILEGES', $msg);
        }

        //xarVarSetCached('security', $cache_name, false);
        return false;
    }

    //xarVarSetCached('security', $cache_name, true);
    return true;
}
?>