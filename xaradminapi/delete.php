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
 * Delete security level(s) based on args passed in.
 *
 * @param array $args
 */
function security_adminapi_delete($args)
{
    extract($args);

    // At this point we must have a modid.
    if( empty($modid) ){ return false; }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['security_roles'];

    $sql = "DELETE FROM $table WHERE modid = ? ";
    $bindvars = array($modid);

    if( isset($itemtype) )
    {
        $sql .= " AND itemtype = ? ";
        $bindvars[] = $itemtype;
    }

    if( isset($itemid) )
    {
        $sql .= " AND itemid = ? ";
        $bindvars[] = $itemid;
    }

    $result = $dbconn->Execute($sql, $bindvars);
    if( !$result ){ return false; }

    return true;
}
?>