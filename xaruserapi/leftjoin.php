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
    Provide SQL info to do a join on the security table

    @param $args['module']
    @param $args['modid']
    @param $args['itemtype']
    @param $args['itemid']
    @param $args['iids']
    @param $args['level']

    @return array
*/
function security_userapi_leftjoin($args)
{
    extract($args);

    $info = array();

    if( xarModIsAvailable('owner') ){ xarModAPILoad('owner', 'user'); }

    if( !isset($level) ){ $level = SECURITY_OVERVIEW; }

    // Get current user and groups
    $currentUserId = xarUserGetVar('uid');
    $groups = array();

    $xartable =& xarDBGetTables();

    $info['iid'] = "{$xartable['security']}.xar_itemid";


    $secTable = $xartable['security'];
    $secGroupLevelTable = $xartable['security_group_levels'];
    $ownerTable = $xartable['owner'];

    $left = array();

    if( !empty($modid) )
    {
        $left[] = " $secTable.xar_modid = $modid ";
    }
    if( !empty($itemtype) )
    {
        $left[] = " $secTable.xar_itemtype = $itemtype ";
    }
    if( !empty($iids) )
    {
        if( is_string($iids) )
            $left[] = "$secTable.xar_itemid = $iids";
        else if( is_array($iids) )
            $where[] = "$secTable.xar_itemid IN ( " . join(', ', $iids) . " )";

    }
    else if( !empty($itemid) )
    {
        $left[] = "$secTable.xar_itemid = $itemid";
    }

    if( count($left) > 0  )
    {
        $left_join = " LEFT JOIN $secTable ON " . join(' AND ', $left);
    }

    // user id field passed in so we can bypass the owner module.
    if( empty($user_field) )
    {
        $left_join .= "
            LEFT JOIN $ownerTable ON
                $secTable.xar_modid    = $ownerTable.xar_modid AND
                $secTable.xar_itemtype = $ownerTable.xar_itemtype AND
                $secTable.xar_itemid   = $ownerTable.xar_itemid
        ";
    }
    $left_join .= "
        LEFT JOIN $secGroupLevelTable ON
            $secTable.xar_modid    = $secGroupLevelTable.xar_modid AND
            $secTable.xar_itemtype = $secGroupLevelTable.xar_itemtype AND
            $secTable.xar_itemid   = $secGroupLevelTable.xar_itemid
    ";

    // User Check
    if( !empty($user_field) )
    {
        $secCheck[] = " ( $secTable.xar_userlevel & $level AND $user_field = $currentUserId ) ";
    }
    else
    {
        $secCheck[] = " ( $secTable.xar_userlevel & $level AND $ownerTable.xar_uid = $currentUserId ) ";
    }

    //Check Groups
    $roles = new xarRoles();
    $user = $roles->getRole($currentUserId);
    $tmp = $user->getParents();
    $parents = array();
    foreach( $tmp as $u ){ $parents[] = $u->uid; }
    if( !empty($limit_gids) and is_array($limit_gids) ){ $parents = array_merge($parents, $limit_gids); }
    foreach( $parents as $parent )
        $secCheck[] = " ( $secGroupLevelTable.xar_gid = $parent AND xar_level & $level ) ";

    // Check for world
    $secCheck[] = " ( $secTable.xar_worldlevel & $level ) ";

    /*
        Admin's always have access to everything (A security level bypass)
        NOTE: But this also allows admins to use other limits or
              exclude params like the $limit_gids var
    */
    if( xarSecurityCheck('AdminPanel', 0) ){ $secCheck[] = " ( 'TRUE' = 'TRUE' ) "; }

    $where[] = " ( " . join(" OR ", $secCheck) . " ) ";
    if( isset($limit_gids) && count($limit_gids) > 0 )
    {
        $where[] = " $secGroupLevelTable.xar_gid IN ( " . join(', ', $limit_gids) . " ) ";
    }

    if( count($where) > 0 )
    {
        $info['where'] = ' ( ' . join(' AND ', $where) . ' ) ';
        if( !empty($exceptions) ){ $info['where'] = " ( {$info['where']} OR $exceptions ) "; }
    }

    $info['left_join'] = $left_join;

    return $info;
}
?>