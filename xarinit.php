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
 * Global things for this file
 *
 */
xarDBLoadTableMaintenanceAPI();


/**
 * initialise the bkview module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function bkview_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $bkviewtable = $xartable['bkview'];
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

    // The above brings the module to it 1.0 version, to prevent duplication of code (and errors) we let the upgrade handle the rest
    return bkview_upgrade('1.0');
}

/**
 * upgrade the bkview module from an old version
 * This function can be called multiple times
 */
function bkview_upgrade($oldversion)
{
    // Upgrade dependent on old version number, hook into the right case
    // The final version stored in the database depends on the version mentioned
    // in the xarversion.php file
    switch($oldversion) {
        // Compatability for pre-three-digit version numbers
    case  1.0 :
    case '1.0':
        // Hook was added
        xarModRegisterHook('item','search','API','bkview','user','search');
    case '1.1.0':
        // Some blocks
        // Summary of committers info
        if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                           array('modName'  => 'bkview',
                                 'blockType'=> 'committers'))) return;
    case '1.2.0':
        // We end with the current version, but dont do anything
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
    // The order is important
    // 1. make the module less functional (hooks, blocks etc.)
    // 2. remove the data
    // 3. remove the security
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $bkviewtable = $xartable['bkview'];

    // Unregister the hooks
    if (!xarModUnregisterHook('item','search','API','bkview','user','search')) return;

    // Unregister the blocks
    if(!xarModAPIFunc('blocks','admin','unregister_block_type',
                      array('modName' => 'bkview',
                            'blockType' => 'committers'))) return;

    // Generate the SQL to drop the table using the API
    $sql = xarDBDropTable($xartable['bkview']);
    if (empty($sql)) return; // throw back

    // Drop the table
    if(!$dbconn->Execute($sql)) return;

    // Remove the masks
    if(!xarRemoveMasks('bkview')) return;

    // Deletion successful
    return true;
}

?>