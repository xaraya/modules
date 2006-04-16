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

    $table = $xartable['security'];
    $groupLevelTable = $xartable['security_group_levels'];

    if( empty($settings) )
    {
        return false;
    }

    $query = "INSERT INTO $table (xar_modid, xar_itemtype, xar_itemid, xar_userlevel, xar_worldlevel)
              VALUES ( ?, ?, ?, ?, ? )
    ";
    $bindvars = array( $modid, $itemtype, $itemid, $settings['levels']['user'], $settings['levels']['world'] );
    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ) return false;

    foreach( $settings['levels']['groups'] as $gid => $group_level )
    {
        // If level is 0 we don't need a record there is an implied deny
        if( $group_level > 0 )
        {
            $query = "INSERT INTO $groupLevelTable (xar_modid, xar_itemtype, xar_itemid, xar_gid, xar_level)
                      VALUES ( ?, ?, ?, ?, ? )
            ";
            $bindvars = array( $modid, $itemtype, $itemid, $gid, $group_level );
            $result = $dbconn->Execute($query, $bindvars);
            if( !$result ) return false;
        }
    }

    return true;
}
?>