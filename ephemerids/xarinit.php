<?php
/**
 * File: $Id$
 *
 * Ephemerids
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Ephemerids Module
 * @author Volodymyr Metenchuk
*/

/**
 * init ephemerids module
 */
function ephemerids_init()
{
    // Get database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // Create tables
    $ephemtable = $xartable['ephem'];

    // adodb does not provide the functionality to abstract table creates
    xarDBLoadTableMaintenanceAPI();

    // Define the table structure
    $fields = array(
        'xar_eid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_did'=>array('type'=>'integer','size'=>'small','size'=>2,'null'=>FALSE,'default'=>'0'),
        'xar_mid'=>array('type'=>'integer','size'=>'small','size'=>2,'null'=>FALSE,'default'=>'0'),
        'xar_yid'=>array('type'=>'integer','size'=>4,'null'=>FALSE,'default'=>'0'),
        'xar_content'=>array('type'=>'text','null'=>FALSE),
        'xar_elanguage'=>array('type'=>'varchar','size'=>32,'null'=>FALSE)
    );

/*
    $ephemcolumn = &$xartable['ephem_column'];
    $query = "CREATE TABLE $ephemtable (
            $ephemcolumn[eid] int(11) NOT NULL auto_increment,
            $ephemcolumn[did] tinyint(2) NOT NULL default '0',
            $ephemcolumn[mid] tinyint(2) NOT NULL default '0',
            $ephemcolumn[yid] int(4) NOT NULL default '0',
            $ephemcolumn[content] text NOT NULL,
            $ephemcolumn[language] varchar(30) NOT NULL default '',
            PRIMARY KEY(xar_eid))";

    $dbconn->Execute($query);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        // Report failed initialisation attempt
        return false;
    }
*/

    // Create the Table
    $query = xarDBCreateTable($ephemtable,$fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Set up module variables
    xarModSetVar('Ephemerids', 'detail', 0);
    xarModSetVar('Ephemerids', 'table', 1);

    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'ephemerids',
                             'blockType'=> 'ephem'))) return;

    // Register Masks
    xarRegisterMask('OverviewEphemerids','All','emphemerids','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadEphemerids','All','emphemerids','All','All','ACCESS_READ');
    xarRegisterMask('EditEphemerids','All','emphemerids','All','All','ACCESS_EDIT');
    xarRegisterMask('AddEphemerids','All','emphemerids','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteEphemerids','All','emphemerids','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminEphemerids','All','emphemerids','All','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade
 */
function ephemerids_upgrade($oldversion)
{
    return true;
}

/**
 * delete the ephemerids module
 */
function ephemerids_delete()
{
    // Get database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // Delete tables
    $query = "DROP TABLE $xartable[ephem]";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Delete module variables
    xarModDelVar('Ephemerids', 'detail');
    xarModDelVar('Ephemerids', 'table');

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