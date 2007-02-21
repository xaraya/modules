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
    Check to see if user has access to a xaraya item

    @deprecated DEPRECATED

    @param int $args['modid']
    @param int $args['itemtype']
    @param int $args['itemid']
    @param int $args['level']

    @return boolean
*/
function security_userapi_check($args)
{
    extract($args);

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

    $secRolesTable = $xartable['security_roles'];

    $bindvars = array();
    $where = array();

    switch( $level )
    {
        case SECURITY_ADMIN:
            $field = 'xadmin';
            break;
        case SECURITY_MANAGE:
            $field = 'xmanage';
            break;
        case SECURITY_WRITE:
            $field = 'xwrite';
            break;
        case SECURITY_COMMENT:
            $field = 'xcomment';
            break;
        case SECURITY_READ:
            $field = 'xread';
            break;
        case SECURITY_OVERVIEW:
            $field = 'xoverview';
            break;
        default:
            $field = 'xread';
    }

    $query = "
        SELECT $field
        FROM $secRolesTable
    ";

    $where[] = "$secRolesTable.modid = ?";
    $bindvars[] = isset($modid) ? (int)$modid : 0;
    $where[] = "$secRolesTable.itemtype = ?";
    $bindvars[] = isset($itemtype) ? (int)$itemtype : 0;
    $where[] = "$secRolesTable.itemid = ?";
    $bindvars[] = isset($itemid) ? (int)$itemid : 0;


    //Check Groups
    $uids = array(0, xarUserGetVar('uid'));
    $roles = new xarRoles();
    $user = $roles->getRole(xarUserGetVar('uid'));
    $tmp = $user->getParents();
    foreach( $tmp as $u )
    {
        $uids[] = $u->uid;
    }
    $where[] = "uid IN (". join(', ', $uids) .")";


    // Check for world
    $where[] = "$field = 1";

    if( count($where) > 0 )
    {
        $query .= ' WHERE ' . join(' AND ', $where);
    }

    $result = $dbconn->Execute($query, $bindvars);

    if( $result->EOF )
    {
        if( empty($hide_exception) )
        {
            $msg = "You do not have the proper security to perform this action!";
            xarErrorSet(XAR_USER_EXCEPTION, 'NO_PRIVILEGES', $msg);
        }

        return false;
    }

    return true;
}
?>