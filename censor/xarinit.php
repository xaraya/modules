<?php
/**
 * File: $Id$
 *
 * Xaraya Censor
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 * @subpackage Censor Module
 * @author John Cox
 */
// Load Table Maintainance API
xarDBLoadTableMaintenanceAPI();

/**
 * initialise the autolinks module
 */
function censor_init()
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // Set up module variables
    xarModSetVar('censor', 'itemsperpage', 20);
    xarModSetVar('censor', 'replace', '****');

    $censortable = $xartable['censor'];

    $fields = array('xar_cid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_keyword' => array('type' => 'varchar', 'size' => 100, 'null' => false, 'default' => ''),
        );

    $query = xarDBCreateTable($censortable, $fields);
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $index = array('name' => 'i_'.xarDBGetSiteTablePrefix().'_censor_1',
        'fields' => array('xar_keyword'),
        'unique' => true);
    $query = xarDBCreateIndex($censortable, $index);
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    // Set up module hooks
    if (!xarModRegisterHook('item',
            'transform',
            'API',
            'censor',
            'user',
            'transform')) return;

    xarRegisterMask('ReadCensor', 'All', 'censor', 'All', 'All', 'ACCESS_READ');
    xarRegisterMask('EditCensor', 'All', 'censor', 'All', 'All', 'ACCESS_EDIT');
    xarRegisterMask('AddCensor', 'All', 'censor', 'All', 'All', 'ACCESS_ADD');
    xarRegisterMask('DeleteCensor', 'All', 'censor', 'All', 'All', 'ACCESS_DELETE');
    xarRegisterMask('AdminCensor', 'All', 'censor', 'All', 'All', 'ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

/**
 * upgrade the smiley module from an old version
 */
function censor_upgrade($oldversion)
{
    return true;
}

/**
 * delete the smiley module
 */
function censor_delete()
{
    // Remove module hooks
    if (!xarModUnregisterHook('item',
            'transform',
            'API',
            'censor',
            'user',
            'transform')) {
        return false;
    }
    // Drop the table
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $censortable = $xartable['censor'];
    $query = xarDBDropTable($censortable);
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    // Remove module variables
    xarModDelVar('censor', 'replace');
    xarModDelVar('censor', 'itemsperpage');
    // Remove Masks
    xarUnRegisterMask('ReadCensor');
    xarUnRegisterMask('EditCensor');
    xarUnRegisterMask('AddCensor');
    xarUnRegisterMask('DeleteCensor');
    xarUnRegisterMask('AdminCensor');
    // Deletion successful
    return true;
}

?>
