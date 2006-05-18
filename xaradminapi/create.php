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
    Creates all security levels

    @param $args['modid']
    @param $args['itemtype']
    @param $args['itemid']
    @param $args['settings']

    @return boolean true if successful otherwise false
*/
function security_adminapi_create($args)
{
    extract($args);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $securityRolesTable = $xartable['security_roles'];

    if( empty($settings) ){ return false; }

    $query = "DELETE FROM $securityRolesTable ";
    $where = array();
    $bindvars = array();
    if( isset($modid) )
    {
        $where[] = " modid = ? ";
        $bindvars[] = $modid;
    }
    if( isset($itemtype) )
    {
        $where[] = " itemtype = ? ";
        $bindvars[] = $itemtype;
    }
    if( isset($itemid) )
    {
        $where[] = " itemid = ? ";
        $bindvars[] = $itemid;
    }

    if( count($where) > 0 )
    {
        $query .= ' WHERE ' . join(" AND ", $where);
    }
    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ) return false;

    foreach( $settings['levels'] as $role_id => $level )
    {
        $query =
            "INSERT INTO $securityRolesTable "
            . "(modid, itemtype, itemid, uid, xoverview, xread, xcomment, xwrite, xmanage, xadmin)  "
            . "VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ) ";

        $bindvars = array(
            isset($modid)      ? $modid    : 0
            , isset($itemtype) ? $itemtype : 0
            , isset($itemid)   ? $itemid   : 0
            , isset($role_id)  ? $role_id  : 0
            , !empty($level['overview'])? 1 : 0
            , !empty($level['read'])    ? 1 : 0
            , !empty($level['comment']) ? 1 : 0
            , !empty($level['write'])   ? 1 : 0
            , !empty($level['manage'])  ? 1 : 0
            , !empty($level['admin'])   ? 1 : 0
        );
        $result = $dbconn->Execute($query, $bindvars);
        if( !$result ) return false;
    }

    return true;
}
?>