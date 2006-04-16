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
    Get the security of a xaraya item

    @param $args['modid']
    @param $args['itemtype'] (optional)
    @param $args['itemid']

    @return array The security levels for a xaraya item
*/
function security_userapi_get($args)
{
    extract($args);

    /*
        Check for required params modid and itemid
    */
    if( empty($modid) )
    {
        $msg = xarML("Missing required param modid");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'MISSING_REQUIRED_PARAM', $msg);
        return false;
    }
    if( empty($itemid) )
    {
        $msg = xarML("Missing required param itemid");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'MISSING_REQUIRED_PARAM', $msg);
        return false;
    }

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['security'];
    $groupLevelsTable = $xartable['security_group_levels'];

    /*
        Get the user and world levels first
        If they don't exist then we can not have group
        levels so we return and empty array()
    */
    $bindvars = array();
    $where = array();
    $query = "
        SELECT xar_userlevel, xar_worldlevel
        FROM $table
    ";
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
    if( count($where) > 0 )
    {
        $query .= ' WHERE ' . join(' AND ', $where);
    }
    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ){ return false; }
    if( $result->EOF ){ return array(); }

    list($u, $w) = $result->fields;
    $level = array('user' => $u, 'world' => $w);

    /*
        Now Get all the group privs
    */
    $query = "
        SELECT xar_gid, xar_level
        FROM $groupLevelsTable
    ";
    if( count($where) > 0 )
    {
        $query .= ' WHERE ' . join(' AND ', $where);
    }
    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ){ return false; }

    $level['groups'] = array();
    while( (list($gid, $l) = $result->fields) != null )
    {
        $level['groups'][$gid] = $l;
        $result->MoveNext();
    }

    return $level;
}
?>