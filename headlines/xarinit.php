<?php
/**
 * File: $Id$
 *
 * Xaraya Headlines
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Headlines Module
 * @author John Cox
 */

// Load Table Maintaince API
xarDBLoadTableMaintenanceAPI();

/**
 * Initialise the headlines module
 *
 * @return bool
 * @raise DATABASE_ERROR
 */
function headlines_init()
{

    // Get database information
    $dbconn =& xarDBGetConn();
    $table =& xarDBGetTables();

    // Create tables
    $headlinesTable = xarDBGetSiteTablePrefix() . '_headlines';

    $query = xarDBCreateTable($headlinesTable,
                             array('xar_hid'        => array('type'        => 'integer',
                                                             'null'        => false,
                                                             'default'     => '0',
                                                             'increment'   => true,
                                                             'primary_key' => true),
                                   'xar_title'      => array('type'        => 'varchar',
                                                             'size'        => 255,
                                                             'null'        => false,
                                                             'default'     => ''),
                                   'xar_desc'       => array('type'        => 'varchar',
                                                             'size'        => 255,
                                                             'null'        => false,
                                                             'default'     => ''),
                                   'xar_url'        => array('type'        => 'varchar',
                                                             'size'        => 255,
                                                             'null'        => false,
                                                             'default'     => ''),
                                   'xar_order'      => array('type'        => 'integer',
                                                             'null'        => false,
                                                             'default'     => '0',
                                                             'increment'   => false)));
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'headlines',
                             'blockType'=> 'rss'))) return;

    // Set up module variables
    xarModSetVar('headlines', 'itemsperpage', 20);

    // Register Masks
    xarRegisterMask('OverviewHeadlines','All','headlines','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadHeadlines','All','headlines','All','All','ACCESS_READ');
    xarRegisterMask('EditHeadlines','All','headlines','All','All','ACCESS_EDIT');
    xarRegisterMask('AddHeadlines','All','headlines','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteHeadlines','All','headlines','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminHeadlines','All','headlines','All','All','ACCESS_ADMIN');

    return true;
}

/**
 * Upgrade the example module from an old version
 *
 * This function can be called multiple times
 *
 * @param string oldVersion old version to upgrade from
 * @return bool
 * @raise DATABASE_ERROR
 */
function headlines_upgrade($oldVersion)
{
    // Upgrade dependent on old version number
    switch($oldVersion) {
        case 0.1:
            // Version 0.1 didn't have a 'order' field, it was added
            // in version 0.2

            // Get database setup
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $headlinestable = $xartable['headlines'];

            // Add a column to the table
            $query = xarDBAlterTable(array('table' => $headlinestable,
                                           'command' => 'add',
                                           'field' => 'xar_order',
                                           'type' => 'integer',
                                           'null' => false,
                                           'default' => '0'));

            // Pass to ADODB, and send exception if the result isn't valid.
            $result =& $dbconn->Execute($query);
            if (!$result) return;

        break;
    }

    // Update successful
    return true;
}

/**
 * Delete the headlines module
 *
 * @returns bool
 */
function headlines_delete()
{

    // need to drop the module tables too
    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['headlines']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'headlines',
                             'blockType'=> 'rss'))) return;

    xarModDelVar('headlines', 'itemsperpage');

    // Remove Masks and Instances
    xarRemoveMasks('headlines');
    xarRemoveInstances('headlines');

    return true;

}

?>