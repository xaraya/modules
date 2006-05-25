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
 * Upgrade security tables from 0.8.* line to 0.9.* line
 *
 * @param unknown_type $args
 */
function security_admin_upgrade($args)
{
    if( !xarSecurityCheck('AdminSecurity') ) return false;

    xarModAPILoad('security');

    if( xarModIsAvailable('owner') ){ xarModAPILoad('owner'); }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    if( isset($xartable['owner']) )
        $owner_table = $xartable['owner'];
    else
        $owner_table = null;

    $sec_table = $xartable['security'];
    $group_table = $xartable['security_group_levels'];
    $roles_table = $xartable['security_roles'];

    // Get current tables so we know what tables need to be converted
    $tables = $datadict->getTables();
    //var_dump($tables);

    // Detects security table to convert
    $group_table_exists = false;
    $sec_table_exists = false;
    foreach( $tables as $table )
    {
        if( $table == $group_table )
        {
            $group_table_exists = true;
        }
        elseif( $table == $sec_table )
        {
            $sec_table_exists = true;
        }
    }

    // Convert the groups table
    if( $group_table_exists == true )
    {
        // Easy stuff first. Lets move the groups stuff into the new roles table.
        $query = " INSERT INTO $roles_table (modid, itemtype, itemid, uid, xoverview, xread, xcomment, xwrite, xmanage, xadmin ) "
            . "SELECT xar_modid, xar_itemtype, xar_itemid, xar_gid, "
            . "IF(xar_level & 32 > 0, 1 , 0), "
            . "IF(xar_level & 16 > 0, 1 , 0), "
            . "IF(xar_level & 8 > 0, 1 , 0), "
            . "IF(xar_level & 4 > 0, 1 , 0), "
            . "IF(xar_level & 2 > 0, 1 , 0), "
            . "IF(xar_level & 1 > 0, 1 , 0) "
            . "FROM $group_table";
        $result = $dbconn->Execute($query);
        if( !$result ){ return false; }

        /* Drop the security tables */
        $result = $datadict->dropTable($group_table);
        if( !$result ){ return false; }
    }

    // Convert the old main security table. This one is a bit more trickey
    if( $sec_table_exists == true )
    {
        $hook_list = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
            array(
                'hookModName' => 'security'
            )
        );
        //var_dump($hook_list);

        foreach( $hook_list as $module_name => $itemtypes )
        {
            $modid = xarModGetIdFromName($module_name);
            foreach( $itemtypes as $itemtype => $crap_value )
            {
                $settings = xarModAPIFunc('security', 'user', 'get_default_settings',
                    array(
                        'modid' => $modid,
                        'itemtype' => $itemtype
                    )
                );
                //var_dump($settings);
                if( count($settings['owner']) == 3 )
                {
                    $owner = $settings['owner'];
                    // Need to join with other table to get what we need
                    // Module itemtype pair is using the owner module.
                    // Join with that table and do a INSERT .. SELECT
                    $query = " INSERT INTO $roles_table (modid, itemtype, itemid, uid, xoverview, xread, xcomment, xwrite, xmanage, xadmin ) "
                        . "SELECT $sec_table.xar_modid, $sec_table.xar_itemtype, $sec_table.xar_itemid, {$owner['table']}.{$owner['column']}, "
                        . "IF(xar_userlevel & 32 > 0, 1 , 0), "
                        . "IF(xar_userlevel & 16 > 0, 1 , 0), "
                        . "IF(xar_userlevel & 8 > 0, 1 , 0), "
                        . "IF(xar_userlevel & 4 > 0, 1 , 0), "
                        . "IF(xar_userlevel & 2 > 0, 1 , 0), "
                        . "IF(xar_userlevel & 1 > 0, 1 , 0) "
                        . "FROM $sec_table "
                        . "LEFT JOIN {$owner['table']} ON {$owner['table']}.{$owner['primary_key']} = $sec_table.xar_itemid "
                        . "WHERE $sec_table.xar_modid  = $modid "
                        . "AND $sec_table.xar_itemtype = $itemtype "
                        ;
                    $result = $dbconn->Execute($query);
                    if( !$result ){ return  false; }
                }
                elseif( xarModIsAvailable('owner') )
                {
                    // Module itemtype pair is using the owner module.
                    // Join with that table and do a INSERT .. SELECT
                    $query = " INSERT INTO $roles_table (modid, itemtype, itemid, uid, xoverview, xread, xcomment, xwrite, xmanage, xadmin ) "
                        . "SELECT $sec_table.xar_modid, $sec_table.xar_itemtype, $sec_table.xar_itemid, $owner_table.xar_uid, "
                        . "IF(xar_userlevel & 32 > 0, 1 , 0), "
                        . "IF(xar_userlevel & 16 > 0, 1 , 0), "
                        . "IF(xar_userlevel & 8 > 0, 1 , 0), "
                        . "IF(xar_userlevel & 4 > 0, 1 , 0), "
                        . "IF(xar_userlevel & 2 > 0, 1 , 0), "
                        . "IF(xar_userlevel & 1 > 0, 1 , 0) "
                        . "FROM $sec_table "
                        . "LEFT JOIN $owner_table ON $owner_table.xar_modid = $sec_table.xar_modid "
                        . "AND $owner_table.xar_itemtype = $sec_table.xar_itemtype "
                        . "AND $owner_table.xar_itemid = $sec_table.xar_itemid "
                        . "WHERE $sec_table.xar_modid  = $modid "
                        . "AND $sec_table.xar_itemtype = $itemtype "
                        ;
                    $result = $dbconn->Execute($query);
                    if( !$result ){ return  false; }
                }
            }
        }


        // Now convert the world values. Should be very easy
        $query = " INSERT INTO $roles_table (modid, itemtype, itemid, uid, xoverview, xread, xcomment, xwrite, xmanage, xadmin ) "
            . "SELECT xar_modid, xar_itemtype, xar_itemid, 0, "
            . "IF(xar_worldlevel & 32 > 0, 1 , 0), "
            . "IF(xar_worldlevel & 16 > 0, 1 , 0), "
            . "IF(xar_worldlevel & 8 > 0, 1 , 0), "
            . "IF(xar_worldlevel & 4 > 0, 1 , 0), "
            . "IF(xar_worldlevel & 2 > 0, 1 , 0), "
            . "IF(xar_worldlevel & 1 > 0, 1 , 0) "
            . "FROM $sec_table";
        $result = $dbconn->Execute($query);
        if( !$result ){ return false; }


        $result = $datadict->dropTable($xartable['security']);
        if( !$result ){ return false; }
    }

    return true;
}

?>