<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 * 
 * Sniffer System
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * @subpackage aniffer module
 * @author Frank Besler using phpSniffer by Roger Raymond 
 */

/**
 * Initialise the mail module
 * 
 * @author Frank Besler 
 * @access public 
 * @param none $ 
 * @return true on success or void or false on failure
 * @throws 'DATABASE_ERROR'
 * @todo nothing
 */
function sniffer_init()
{ 
    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables(); 
    // Load Table Maintainance API
    xarDBLoadTableMaintenanceAPI(); 
    // Create the Table
    $systemPrefix = xarDBGetSystemTablePrefix();
    $xartable['sniffer'] = $systemPrefix . '_sniffer';
    $query = xarDBCreateTable($xartable['sniffer'],
        array('xar_ua_id' => array('type' => 'integer', 'size' => 'small',
                'unsigned' => true, 'null' => false,
                'default' => '0', 'increment' => true,
                'primary_key' => true),
            'xar_ua_agent' => array('type' => 'varchar', 'size' => 254,
                'null' => false),
            'xar_ua_osnam' => array('type' => 'varchar', 'size' => 40,
                'null' => false),
            'xar_ua_osver' => array('type' => 'varchar', 'size' => 20,
                'null' => false),
            'xar_ua_agnam' => array('type' => 'varchar', 'size' => 40,
                'null' => false),
            'xar_ua_agver' => array('type' => 'varchar', 'size' => 20,
                'null' => false),
            'xar_ua_cap' => array('type' => 'text'),
            'xar_ua_quirk' => array('type' => 'text')
            ));
    if (empty($query)) return false; // throw back
     
    // Pass the Table Create DDL to adodb to create the table
    $result = &$dbconn->Execute($query);
    if (!$result) return false; 
    // set index
    $query = xarDBCreateIndex($xartable['sniffer'],
        array('name' => 'i_' . xarDBGetSiteTablePrefix() . '_sniff_ag',
            'fields' => array('xar_ua_agent'),
            'unique' => true));

    $result = &$dbconn->Execute($query);
    if (!$result) return false; 
    // sniff the installing user
    include_once('modules/sniffer/xaruserapi.php');
    sniffer_userapi_sniffbasic(''); 
    // Initialisation successful
    return true;
} 

/**
 * Upgrade the mail module from an old version
 * 
 * @author Frank Besler 
 * @access public 
 * @param  $oldVersion 
 * @return true on success or false on failure
 * @throws no exceptions
 * @todo nothing
 */
function sniffer_upgrade($oldversion)
{ 
    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables(); 
    // load the table maintenance API
    xarDBLoadTableMaintenanceAPI(); 
    // Upgrade dependent on old version number
    switch ($oldVersion) {
        case 0.01:
            break;
            // case '0.0.1':
            // break;
    } 
    return true;
} 

/**
 * Delete the mail module
 * 
 * @author Frank Besler 
 * @access public 
 * @param no $ parameters
 * @return true on success or false on failure
 * @todo restore the default behaviour prior to 1.0 release
 */
function sniffer_delete()
{ 
    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables(); 
    // load the table maintenance API
    xarDBLoadTableMaintenanceAPI(); 
    // Drop the table
    $query = xarDBDropTable($xartable['sniffer']);
    if (empty($query)) return; // throw back
     
    // Drop the table
    $result = &$dbconn->Execute($query); 
    // Check for an error with the database code, and if so raise the
    if (!$result) return false; 
    // Remove Masks and Instances
    xarRemoveMasks('sniffer');
    xarRemoveInstances('sniffer'); 
    // Deletion successful
    return true;
} 

?>