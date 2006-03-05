<?php
/**
 * Owner - Tracks who creates xaraya based items.
 *
 * @package Xaraya Modules
 * @copyright (C) 2003-2005 by Envision Net, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.envisionnet.net/
 *
 * @subpackage Owner module
 * @link http://www.envisionnet.net/home/products/security/
 * @author Brian McGilligan <brian@envisionnet.net>
 */
/**
    Get all owner information

    @param $args['modid'] (optional)
    @param $args['itemtype'] (optional)
    @param $args['itemid'] (optional)

    @return array contains owner information
*/
function owner_userapi_getall($args)
{
    extract($args);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pre = xarDBGetSiteTablePrefix();
    $table = $xartable['owner'];

    $bindvars = array();
    $where = array();
    $query = "
        SELECT xar_modid, xar_itemtype, xar_itemid, xar_uid
        FROM $table
    ";

    if( !empty($modid) )
    {
        $where[] = ' xar_modid = ? ';
        $bindvars[] = $modid;
    }
    if( !empty($itemtype) )
    {
        $where[] = ' xar_itemtype = ? ';
        $bindvars[] = $itemtype;
    }
    if( !empty($itemid) )
    {
        $where[] = ' xar_itemid = ? ';
        $bindvars[] = $itemid;
    }
    if( count($where) > 0 )
    {
        $query .= ' WHERE ' . join(' AND ', $where);
    }
    $result = $dbconn->Execute($query, $bindvars);

    $owners = array();
    while( (list($modid, $itemtype, $itemid, $uid) = $result->fields) != null )
    {
        $owners[$modid][$itemtype][$itemid] = array('uid' => $uid);
        $result->MoveNext();
    }

    return $owners;
}
?>