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
    
     $fields = array('xar_repoid'  => array('type'=>'integer' ,'null'=>false,'increment'=>true,'primary_key'=>true),
                     'xar_name'   => array('type'=>'varchar','size'=>20   ,'null'=>false),
                     'xar_path' => array('type'=>'varchar','size' => 254, 'null'=>false,'default'=>'/var/bk/repo'),
     'xar_repotype' => array('type'=>'integer', 'null'=>false,'default'=>'1'),
     'xar_lod' => array('type'=>'varchar', 'size' => 10,'null'=>false,'default'=>'')
                    );

    $query = xarDBCreateTable($bkviewtable,$fields);
    $dbconn->Execute($query);
    
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
        // add the repository type column
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        $bkviewtable = $xartable['bkview'];
        // Get a data dictionary object with item create methods.
        /*$datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
        
        $changes ="
            xar_repotype I      NotNull DEFAULT 1,
            xar_lod      C(100) NotNull DEFAULT ''";
        $result = $datadict->ChangeTable($bkviewtable, $changes);*/
        // Since we modified the database we're bumping the main revision number
    case '2.0.0':
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

    // Drop the table
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERDATABASE');
    $result = $datadict->dropTable($xartable['bkview']);
    if(!$result) return;
    
    // Remove the masks
    if(!xarRemoveMasks('bkview')) return;

    // Deletion successful
    return true;
}

?>