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
    Insert a owner of an item into the db

    @param $args['modid'] (required)
    @param $args['itemtype'] (optional)
    @param $args['itemid'] (required)
    @param $args['uid'] The user id who owns the item (optioanl)

    @return boolean true if successful otherwise false
*/
function owner_adminapi_create($args)
{
    extract($args);

    /*
        Check for required vars. If any are missing we
        can not do anything so just quit
    */
    if( empty($modid) ){ return false; }
    if( empty($itemid) ){ return false; }

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table    =  $xartable['owner'];

    /*
        Init. optional vars
    */
    if( !isset($itemtype) ){ $itemtype = 0; }
    if( empty($uid) ){ $uid = xarUserGetVar('uid'); }

    $query = "
        INSERT INTO $table (xar_modid, xar_itemtype, xar_itemid, xar_uid)
        VALUES ( ?, ?, ?, ? )
    ";
    $bindvars = array( $modid, $itemtype, $itemid, $uid );
    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ) return false;

    return true;
}
?>