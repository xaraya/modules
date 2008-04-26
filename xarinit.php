<?php
/**
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
 * @throws DATABASE_ERROR
 */
function headlines_init()
{

    // Get database information
    $dbconn =& xarDBGetConn();
    $table =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

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
                                                             'increment'   => false),
                                   'xar_string'     => array('type'        => 'varchar',
                                                             'size'        => 255,
                                                             'null'        => false,
                                                             'default'     => ''),
                                   'xar_date'       => array('type'        => 'integer',
                                                             'unsigned'    => TRUE,
                                                             'null'        => FALSE,
                                                             'default'     => '0'),
                                   'xar_settings'   => array ('type' => 'text')));
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . $headlinesTable . '_hid',
                   'fields'    => array('xar_hid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($headlinesTable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'headlines',
                             'blockType'=> 'rss'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'headlines',
                             'blockType'=> 'cloud'))) return;

    // Set up module variables
    xarModSetVar('headlines', 'itemsperpage', 20);
    xarModSetVar('headlines','importpubtype', 0);
    xarModSetVar('headlines','showfeeds', '');
    xarModSetVar('headlines', 'uniqueid', 'feed;link');
    // added in 0.9.0
    xarModSetVar('headlines', 'SupportShortURLs', 1);    
    // added in 1.1.0
    xarModSetVar('headlines', 'parser', 'default');
    // added > 1.1.0
    xarModSetVar('headlines', 'feeditemsperpage', 20);
    xarModSetVar('headlines','maxdescription', 0);
    xarModSetVar('headlines','showcomments', 0);
    xarModSetVar('headlines', 'showratings', 0);
    xarModSetVar('headlines', 'showhitcount', 0);
    xarModSetVar('headlines','showkeywords', 0);
    xarModSetVar('headlines','useModuleAlias', 0);
    xarModSetVar('headlines', 'aliasname', '');
    // added in 1.2.1
    xarModSetVar('headlines', 'adminajax', 0);
    xarModSetVar('headlines', 'userajax', 0);

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
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $headlinesTable = $xartable['headlines'];

    xarDBLoadTableMaintenanceAPI();

    // Upgrade dependent on old version number
    switch($oldVersion) {
        case '0.1':
            // Version 0.1 didn't have a 'order' field, it was added
            // in version 0.2

            // Add a column to the table
            $query = xarDBAlterTable(array('table' => $headlinesTable,
                                           'command' => 'add',
                                           'field' => 'xar_order',
                                           'type' => 'integer',
                                           'null' => false,
                                           'default' => '0'));

            // Pass to ADODB, and send exception if the result isn't valid.
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            // fall through to next upgrade

        case '0.2':
        case '0.2.0':
            xarModSetVar('headlines', 'SupportShortURLs', 1);
            // fall through to next upgrade

        case '0.9':
        case '0.9.0':
            // Index the hid field
            $index = array('name'      => 'i_' . $headlinesTable . '_hid',
                           'fields'    => array('xar_hid'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($headlinesTable,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // Two New Fields for the Cloud
            $query = xarDBAlterTable($headlinesTable,
                              array('command' => 'add',
                                    'field'   => 'xar_string',
                                    'type'    => 'varchar',
                                    'size'        => 255,
                                    'null'        => false,
                                    'default'     => ''));
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            // Two New Fields for the Cloud
            $query = xarDBAlterTable($headlinesTable,
                              array('command'     => 'add',
                                    'field'       => 'xar_date',
                                    'type'        => 'integer',
                                    'unsigned'    => TRUE,
                                    'null'        => FALSE,
                                    'default'     => '0'));
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            if (!xarModAPIFunc('blocks',
                               'admin',
                               'register_block_type',
                               array('modName'  => 'headlines',
                                     'blockType'=> 'cloud'))) return;
            // fall through to next upgrade

       case '1.0.0':
            // fall through to next upgrade

       case '1.0.1': // To 1.1.0
           // Replace the 'magpie' variable with a more general 'parser' variable.
           $magpie = xarModGetVar('headlines', 'magpie');
           xarModSetVar('headlines', 'parser', (!empty($magpie) ? $magpie : 'default'));
           xarModDelVar('headlines', 'magpie');

       case '1.1.0': // To 1.2.0
            // Added module variables for new options
            xarModSetVar('headlines', 'feeditemsperpage', 20);
            xarModSetVar('headlines','maxdescription', 0);
            xarModSetVar('headlines','showcomments', 0);
            xarModSetVar('headlines', 'showratings', 0);
            xarModSetVar('headlines', 'showhitcount', 0);
            xarModSetVar('headlines','showkeywords', 0);
            xarModSetVar('headlines','useModuleAlias', 0);
            xarModSetVar('headlines', 'aliasname', '');
       case '1.2.0': // To 1.2.1
            xarModSetVar('headlines', 'adminajax', 0);
            xarModSetVar('headlines', 'userajax', 0);
            // New column for per feed settings
            $query = xarDBAlterTable($headlinesTable,array(
                                           'command' => 'add',
                                           'field' => 'xar_settings',
                                           'type' => 'text'));

            // Pass to ADODB, and send exception if the result isn't valid.
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        case '1.2.1': // Current Version To 1.2.2  

        case '1.2.2': // Next Version
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

    xarDBLoadTableMaintenanceAPI();

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
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'headlines',
                             'blockType'=> 'cloud'))) return;
    xarModDelAllVars('headlines');
    xarRemoveMasks('headlines');
    xarRemoveInstances('headlines');
    return true;
}
?>
