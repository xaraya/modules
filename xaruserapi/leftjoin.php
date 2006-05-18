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

    if( !isset($level) ){ $level = SECURITY_OVERVIEW; }

    // Get current user and groups
    $currentUserId = xarUserGetVar('uid');
    $groups = array();

    $xartable =& xarDBGetTables();

    $info['iid'] = "{$xartable['security']}.itemid";

    $secRolesTable = $xartable['security_roles'];

    $where = array();

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

    $secCheck = array();

    // world is 0.
    $uids = array(0, $currentUserId);

    //Check Groups
    $roles = new xarRoles();
    $user = $roles->getRole($currentUserId);
    $tmp = $user->getParents();
    $parents = array();
    //  Listing all uids
    if( !isset($limit_gids)  ){ $limit_gids = array(); }
    foreach( $tmp as $u )
    {
        if( count($limit_gids) > 0 )
        {
            if( in_array($u->uid, $limit_gids) ){ $uids[] = $u->uid; }
        }
        else
        {
            $uids[] = $u->uid;
        }
    }
    $where[] = "uid IN (". join(', ', $uids) .")  ";

    // TODO add switch for various levels
    switch( $level )
    {
        case SECURITY_ADMIN:
            $where[] = "xadmin = 1";
            break;
        case SECURITY_MANAGE:
            $where[] = "xmanage = 1";
            break;
        case SECURITY_WRITE:
            $where[] = "xwrite = 1";
            break;
        case SECURITY_COMMENT:
            $where[] = "xcomment = 1";
            break;
        case SECURITY_READ:
            $where[] = "xread = 1";
            break;
        case SECURITY_OVERVIEW:
            $where[] = "xoverview = 1";
            break;
        default:
            $where[] = "xread = 1";
    }

    /*
        Admin's always have access to everything (A security level bypass)
        NOTE: But this also allows admins to use other limits or
              exclude params like the $limit_gids var
    */
    if( xarSecurityCheck('AdminPanel', 0) ){ $exceptions = " 'TRUE' = 'TRUE' "; }

    if( count($where) > 0 )
    {
        $info['where'] = "(SELECT COUNT(*) FROM xar_security_roles
            WHERE "  . join(' AND ', $where) . " > 0 )";
        if( !empty($exceptions) ){ $info['where'] = " ({$info['where']} OR $exceptions) "; }
    }

    return $info;
}
?>