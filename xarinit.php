<?php
/**
 * Ephemerids
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ephemerids Module
 * @link http://xaraya.com/index.php/release/15.html
 * @author Volodymyr Metenchuk
 */
/**
 * init ephemerids module
 */
function ephemerids_init()
{
    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Create tables
    $ephemtable = $xartable['ephem'];

    // adodb does not provide the functionality to abstract table creates
    xarDBLoadTableMaintenanceAPI();

    // Define the table structure
    $fields = array(
        'xar_eid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_tid'=>array('type'=>'integer','size'=>4,'null'=>FALSE,'default'=>'1'),
        'xar_did'=>array('type'=>'integer','size'=>'small','size'=>2,'null'=>FALSE,'default'=>'0'),
        'xar_mid'=>array('type'=>'integer','size'=>'small','size'=>2,'null'=>FALSE,'default'=>'0'),
        'xar_yid'=>array('type'=>'integer','size'=>4,'null'=>FALSE,'default'=>'0'),
        'xar_content'=>array('type'=>'text','null'=>FALSE),
        'xar_elanguage'=>array('type'=>'varchar','size'=>32,'null'=>FALSE)
    );

    // Create the Table
    $query = xarDBCreateTable($ephemtable,$fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Set up module variables
    xarModSetVar('ephemerids', 'itemsperpage', 20);

    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'ephemerids',
                             'blockType'=> 'ephem'))) return;

    // Register Masks
    xarRegisterMask('OverviewEphemerids','All','ephemerids','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadEphemerids','All','ephemerids','All','All','ACCESS_READ');
    xarRegisterMask('EditEphemerids','All','ephemerids','All','All','ACCESS_EDIT');
    xarRegisterMask('AddEphemerids','All','ephemerids','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteEphemerids','All','ephemerids','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminEphemerids','All','ephemerids','All','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade
 */
function ephemerids_upgrade($oldversion)
{
    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ephemtable = $xartable['ephem'];

    xarDBLoadTableMaintenanceAPI();

    // Upgrade dependent on old version number
    switch($oldversion) {
        case '1.4.0':
            // Code to upgrade from version 1.3 goes here
            xarModSetVar('ephemerids', 'itemsperpage', 20);
            xarModDelVar('ephemerids', 'detail');
            xarModDelVar('ephemerids', 'table');
        case '1.4.1':
            // Code to upgrade from version 1.4 goes here
            $changes = array('command'     => 'add',
                             'field'       => 'xar_tid',
                             'type'        => 'integer',
                             'null'        => false,
                             'default'     => '1');
            $query = xarDBAlterTable($ephemtable, $changes);
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            // Close result set
            $result->Close();

            break;
    }
    return true;
}

/**
 * delete the ephemerids module
 */
function ephemerids_delete()
{
    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Delete tables
    $query = "DROP TABLE $xartable[ephem]";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Delete module variables
    xarModDelVar('ephemerids', 'itemsperpage');

    // Remove Masks and Instances
    xarRemoveMasks('ephemerids');
    xarRemoveInstances('ephemerids');

    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'ephemerids',
                             'blockType'=> 'ephem'))) return;

    // Deletion successful
    return true;
}

?>