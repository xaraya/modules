<?php
/**
 * File: $Id$
 * 
 * Xaraya Site Cloud
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Site Cloud Module
 * @author John Cox
*/

// Load Table Maintaince API
xarDBLoadTableMaintenanceAPI();

/**
 * Initialise the sitecloud module
 *
 * @return bool
 * @raise DATABASE_ERROR
 */
function sitecloud_init()
{

    // Get database information
    $dbconn =& xarDBGetConn();
    $table =& xarDBGetTables();

    // Create tables
    $sitecloudTable = xarDBGetSiteTablePrefix() . '_sitecloud';

    $query = xarDBCreateTable($sitecloudTable,
                             array('xar_id'         => array('type'        => 'integer',
                                                             'null'        => false,
                                                             'default'     => '0',
                                                             'increment'   => true,
                                                             'primary_key' => true),
                                   'xar_title'      => array('type'        => 'varchar',
                                                             'size'        => 255,
                                                             'null'        => false,
                                                             'default'     => ''),
                                   'xar_url'        => array('type'        => 'varchar',
                                                             'size'        => 255,
                                                             'null'        => false,
                                                             'default'     => ''),
                                   'xar_string'     => array('type'        => 'varchar',
                                                             'size'        => 255,
                                                             'null'        => false,
                                                             'default'     => ''),
                                   'xar_date'       => array('type'        => 'integer',
                                                             'unsigned'    => TRUE,
                                                             'null'        => FALSE,
                                                             'default'     => '0')));


    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'sitecloud',
                             'blockType'=> 'cloud'))) return;

    // Set up module variables
    xarModSetVar('sitecloud', 'itemsperpage', 50);

    // Register Masks
    xarRegisterMask('Overviewsitecloud','All','sitecloud','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('Readsitecloud','All','sitecloud','All','All','ACCESS_READ');
    xarRegisterMask('Editsitecloud','All','sitecloud','All','All','ACCESS_EDIT');
    xarRegisterMask('Addsitecloud','All','sitecloud','All','All','ACCESS_ADD');
    xarRegisterMask('Deletesitecloud','All','sitecloud','All','All','ACCESS_DELETE');
    xarRegisterMask('Adminsitecloud','All','sitecloud','All','All','ACCESS_ADMIN');

    return true;
}

/**
 * Delete the sitecloud module
 *
 * @returns bool
 */
function sitecloud_delete()
{

    // need to drop the module tables too
    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['sitecloud']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'sitecloud',
                             'blockType'=> 'cloud'))) return;

    xarModDelVar('sitecloud', 'itemsperpage');

    // Remove Masks and Instances
    xarRemoveMasks('sitecloud');
    xarRemoveInstances('sitecloud');

    return true;

}
?>