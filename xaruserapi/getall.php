<?php
/**
 * Owner - Tracks who creates xaraya based items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Owner Module
 * @author Brian McGilligan <brian@mcgilligan.us>
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