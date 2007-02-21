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
    Creates all security levels

    @param $args['modid']
    @param $args['itemtype']
    @param $args['itemid']
    @param $args['settings']

    @return boolean true if successful otherwise false
*/
function security_adminapi_create($security)
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $securityRolesTable = $xartable['security_roles'];

    $query = "DELETE FROM $securityRolesTable ";
    $where = array();
    $bindvars = array();
    $where[] = " modid = ? ";
    $bindvars[] = $security->modid;
    $where[] = " itemtype = ? ";
    $bindvars[] = $security->itemtype;
    $where[] = " itemid = ? ";
    $bindvars[] = $security->itemid;

    if( count($where) > 0 )
    {
        $query .= ' WHERE ' . join(" AND ", $where);
    }
    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ) return false;

    foreach( $security->levels as $role_id => $level )
    {
        $query =
            "INSERT INTO $securityRolesTable "
            . "(modid, itemtype, itemid, uid, xoverview, xread, xcomment, xwrite, xmanage, xadmin)  "
            . "VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ) ";
        $bindvars = array(
            $security->modid
            , $security->itemtype
            , $security->itemid
            , $role_id
            , $level->overview
            , $level->read
            , $level->comment
            , $level->write
            , $level->manage
            , $level->admin
        );

        $result = $dbconn->Execute($query, $bindvars);
        if( !$result ) return false;
    }

    return true;
}
?>