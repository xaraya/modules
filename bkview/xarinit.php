<?php

/**
 * File: $Id$
 *
 * Initialization of bkview module
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@hsdev.com>
*/

/**
 * initialise the bkview module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function bkview_init()
{
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $bkviewtable = $xartable['bkview'];

    xarDBLoadTableMaintenanceAPI();

    $fields = array(
        'xar_repoid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_name'=>array('type'=>'varchar','size'=>32,'null'=>FALSE),
        'xar_path'=>array('type'=>'varchar','size'=>254,'null'=>FALSE,'default'=>'/var/bk/repo')
    );

    $sql = xarDBCreateTable($bkviewtable,$fields);
    if (empty($sql)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    if (!$dbconn->Execute($sql)) return;


    $instancequery="SELECT DISTINCT xar_name FROM ". $xartable['bkview'];
    $instance = array (
                       array('header' => 'Repository name',
                             'query'  => $instancequery,
                             'limit'  => 20)
                       );
    xarDefineInstance('bkview','Repository',$instance);

    // xarRegisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    xarRegisterMask('ViewAllRepositories','All','bkview','All','All','ACCESS_READ','Being able to view information contained in a repository');
    xarRegisterMask('AdminAllRepositories','All','bkview','All','All','ACCESS_ADMIN','Being able to administer repositories');
    return true;
}

/**
 * upgrade the bkview module from an old version
 * This function can be called multiple times
 */
function bkview_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case 1.0:
            // Code to upgrade from version 1.0 goes here
            break;
        case 2.0:
            // Code to upgrade from version 2.0 goes here
            break;
    }

    // Update successful
    return true;
}

/**
 * delete the bkview module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function bkview_delete()
{
    // Get database setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For xarDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // adodb does not provide the functionality to abstract table creates
    // across multiple databases.  Xaraya offers the xarDropeTable function
    // contained in the following file to provide this functionality.
    xarDBLoadTableMaintenanceAPI();

    // Generate the SQL to drop the table using the API
    $sql = xarDBDropTable($xartable['bkview']);
    if (empty($sql)) return; // throw back

    // Drop the table
    if(!$dbconn->Execute($sql)) return;

    // Deletion successful
    return true;
}

?>