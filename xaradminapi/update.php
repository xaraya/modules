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
function security_adminapi_update($args)
{
    extract($args);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['security'];
    $groupTable = $xartable['security_group_levels'];

    if( empty($settings) ){ return false; }

    $query = "UPDATE $table
        SET xar_userlevel = ?,
            xar_worldlevel = ?
    ";

    $bindvars = array($settings['levels']['user'], $settings['levels']['world']);
    $where = array();
    if( isset($modid) )
    {
        $where[] = " xar_modid = ? ";
        $bindvars[] = $modid;
    }
    if( isset($itemtype) )
    {
        $where[] = " xar_itemtype = ? ";
        $bindvars[] = $itemtype;
    }
    if( isset($itemid) )
    {
        $where[] = " xar_itemid = ? ";
        $bindvars[] = $itemid;
    }

    if( count($where) > 0 )
        $query .= ' WHERE ' . join(" AND ", $where);

    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ) return false;

    //  now process groups
    foreach( $settings['levels']['groups'] as $gid => $group_level )
    {
        $query = "UPDATE $groupTable
            SET xar_level = ?
        ";
        $bindvars = array($group_level);
        $where = array();
        if( !empty($modid) )
        {
            $where[] = " xar_modid = ? ";
            $bindvars[] = $modid;
        }
        if( !empty($itemtype) )
        {
            $where[] = " xar_itemtype = ? ";
            $bindvars[] = $itemtype;
        }
        if( !empty($itemid) )
        {
            $where[] = " xar_itemid = ? ";
            $bindvars[] = $itemid;
        }
        if( !empty($gid) )
        {
            $where[] = " xar_gid = ? ";
            $bindvars[] = $gid;
        }

        if( count($where) > 0 )
            $query .= ' WHERE ' . join(" AND ", $where);

        $result = $dbconn->Execute($query, $bindvars);
        if( !$result ) return false;
    }

    return true;
}
?>