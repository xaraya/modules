<?php
/**
 * File: $Id: s.xarinit.php 1.17 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 *
 * sitetools initialization functions
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage example
 * @author jojodee, http://xaraya.athomeandabout.com
 */

/**
 * initialise the example module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function sitetools_init()
{
    // Get datbase setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $sitetoolstable = $xartable['sitetools'];

    xarDBLoadTableMaintenanceAPI();
    // Define the table structure in this associative array
    // There is one element for each field.  The key for the element is
    // the physical field name.  The element contains another array specifying the
    // data type and associated parameters
    $fields = array('xar_stid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
                    'xar_stgained' => array('type'=>'float', 'size' =>'decimal', 'width'=>12, 'decimals'=>2)
                );

    $query = xarDBCreateTable($sitetoolstable, $fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Set up an initial value for a module variable.
    xarModSetVar('sitetools','adocachepath',xarCoreGetVarDirPath()."/cache/adodb");
    xarModSetVar('sitetools','rsscachepath', xarCoreGetVarDirPath()."/cache/rss");
    xarModSetVar('sitetools','templcachepath', xarCoreGetVarDirPath()."/cache/templates");

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('ReadSiteToolsBlock', 'All', 'sitetools', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ViewSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

/**
 * upgrade the example module from an old version
 * This function can be called multiple times
 */
function sitetools_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case 0.1:

            return sitetools_upgrade(1.0);
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
 * delete the example module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function sitetools_delete()
{
    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['sitetools']);
    if (empty($query)) {
    //return; // let's let delete go to completion. Show error message.
    }
    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Delete any module variables
    xarModDelVar('sitetools','adocachepath');
    xarModDelVar('sitetools','rsscachepath');
    xarModDelVar('sitetools','templcachepath');

    // Remove Masks and Instances

    xarRemoveMasks('sitetools');

    // Deletion successful
    return true;
}

?>
