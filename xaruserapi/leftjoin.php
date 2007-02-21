<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
    Provide SQL info to do a join on the security table

    @deprecated use Security::leftjoin instead

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

    if( !isset($level) ){ $level = SECURITY_OVERVIEW; }
    if( !isset($exceptions) ){ $exceptions = array(); }

    // Get current user and groups
    $currentUserId = xarUserGetVar('uid');
    $groups = array();

    $xartable =& xarDBGetTables();

    $info['iid'] = "{$xartable['security']}.itemid";

    $secRolesTable = $xartable['security_roles'];

    $where = array();
    $join = array();
    if( !empty($modid) ){ $where[] = "$secRolesTable.modid = $modid "; }
    if( !empty($itemtype) ){ $where[] = "$secRolesTable.itemtype = $itemtype "; }
    if( !empty($iids) )
    {
        if( is_string($iids) )
            $where[] = "$secRolesTable.itemid = $iids";
        else if( is_array($iids) )
            $where[] = "$secRolesTable.itemid IN ( " . join(', ', $iids) . " )";
    }
    else if( !empty($itemid) )
    {
        $where[] = "$secRolesTable.itemid = $itemid";
    }

    //Check Groups
    if( isset($limit_gids) and count($limit_gids) > 0 )
    {
        $uids = $limit_gids;
    }
    else
    {
        $roles = new xarRoles();
        $user = $roles->getRole($currentUserId);
        $tmp = $user->getParents();
        $uids = array(0, $currentUserId);
        foreach( $tmp as $u )
        {
            $uids[] = $u->uid;
        }
    }
    $where[] = "$secRolesTable.uid IN (". join(', ', $uids) .")  ";

    switch( $level )
    {
        case SECURITY_ADMIN:
            $level = "$secRolesTable.xadmin = 1";
            break;
        case SECURITY_MANAGE:
            $level = "$secRolesTable.xmanage = 1";
            break;
        case SECURITY_WRITE:
            $level = "$secRolesTable.xwrite = 1";
            break;
        case SECURITY_COMMENT:
            $level = "$secRolesTable.xcomment = 1";
            break;
        case SECURITY_READ:
            $level = "$secRolesTable.xread = 1";
            break;
        case SECURITY_OVERVIEW:
            $level = "$secRolesTable.xoverview = 1";
            break;
        default:
            $level = "$secRolesTable.xread = 1";
    }

    /*
        Admin's always have access to everything (A security level bypass)
        NOTE: But this also allows admins to use other limits or
              exclude params like the $limit_gids var
    */
    if( Security::check(SECURITY_ADMIN, 'security', 0, 0, false) )
    {
        $skip_exceptions = true;
        // Still needed if limit_gids is set
        $exceptions[] = " 'TRUE' = 'TRUE' ";
    }

    if( !empty($exceptions) )
    {
        if( isset($limit_gids) and count($limit_gids) > 0 )
        {
            $where[] = " ( $level OR " . join(' OR ', $exceptions) . ") ";
        }
        else
        {
             // Admin user and no limit are needed so we do not need to do anything
             if( isset($skip_exceptions) ){ $where = array(); }
             else{ $where[] = " $level OR " . join(' OR ', $exceptions) . " "; }
        }
    }
    else
    {
        $where[] = " $level ";
    }


    if( count($where) > 0 )
    {
        $info['where'] = "( SELECT count(*) > 0 FROM {$secRolesTable} "
            . "WHERE "  . join(' AND ', $where) . " )";
    }

    return $info;
}
?>