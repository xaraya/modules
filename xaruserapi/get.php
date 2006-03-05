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
    Get owner a owner of a xaraya item

    @param $args['modid'] (required)
    @param $args['itemtype'] (optional)
    @param $args['itemid'] (required)

    @return array contains owner information
*/
function owner_userapi_get($args)
{
    extract($args);

    if( empty($modid) )
    {
        // need to throw exception
        return false;
    }

    if( empty($itemid) )
    {
        // need to throw exception
        return false;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pre = xarDBGetSiteTablePrefix();
    $table = $xartable['owner'];

    $bindvars = array();
    $where = array();
    $query = "
        SELECT xar_uid
        FROM $table
    ";

    $where[] = ' xar_modid = ? ';
    $bindvars[] = $modid;

    if( !empty($itemtype) )
    {
        $where[] = ' xar_itemtype = ? ';
        $bindvars[] = $itemtype;
    }

    $where[] = ' xar_itemid = ? ';
    $bindvars[] = $itemid;

    if( count($where) > 0 )
    {
        $query .= ' WHERE ' . join(' AND ', $where);
    }
    $result = $dbconn->Execute($query, $bindvars);
    if( $result->EOF ) return array();

    list($uid) = $result->fields;

    return array('uid' => $uid);
}
?>