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

    $table = $xartable['security_roles'];

    /*
        Get the user and world levels first
        If they don't exist then we can not have group
        levels so we return and empty array()
    */
    $bindvars = array();
    $where = array();
    $query = "
        SELECT uid, xoverview, xread, xcomment, xwrite, xmanage, xadmin
        FROM $table
    ";
    if( !empty($modid) )
    {
        $where[] = " modid = ? ";
        $bindvars[] = $modid;
    }
    if( !empty($itemtype) )
    {
        $where[] = " itemtype = ? ";
        $bindvars[] = $itemtype;
    }
    if( !empty($itemid) )
    {
        $where[] = " itemid = ? ";
        $bindvars[] = $itemid;
    }
    if( count($where) > 0 )
    {
        $query .= ' WHERE ' . join(' AND ', $where);
    }
    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ){ return false; }
    if( $result->EOF ){ return array(); }

    $level = array();
    while( (list($uid, $overview, $read, 
    	$comment, $write, $manage, $admin) = $result->fields) != null )
    {
        $level[$uid] = array(
        	'overview' => $overview,
        	'read' 	   => $read,
        	'comment'  => $comment,
        	'write'    => $write,
        	'manage'   => $manage,
        	'admin'    => $admin
        );
        $result->MoveNext();
    }

    if( !isset($level[0]) )
    { 
        $level[0] = array(
        	'overview' => 0,
        	'read' 	   => 0,
        	'comment'  => 0,
        	'write'    => 0,
        	'manage'   => 0,
        	'admin'    => 0
        ); 
    }

    return $level;
}
?>