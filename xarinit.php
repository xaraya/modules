<?php
/**
 * File: $Id: s.xarinit.php 1.11 03/01/18 11:39:31-05:00 John.Cox@mcnabb. $
 *
 * Xaraya Referers
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Referer Module
 * @author John Cox et al.
*/

xarDBLoadTableMaintenanceAPI();
function referer_init()
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $referertable = $xartable['referer'];
    // *_user_data
    $query = xarDBCreateTable($referertable,
                             array('xar_rid'         => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '0',
                                                              'increment'   => true,
                                                              'primary_key' => true),
                                   'xar_url'         => array('type'        => 'varchar',
                                                              'size'        => 254,
                                                              'null'        => false,
                                                              'default'     => ''),
                                   'xar_frequency'   => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '1',
                                                              'increment'   => false),
                                   'xar_time'        => array('type'        =>'integer', 
                                                              'unsigned'    =>TRUE, 
                                                              'null'        =>FALSE,
                                                              'default'     =>'0')));
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    // Register Masks
    xarRegisterMask('OverviewReferer','All','referer','All','All','ACCESS_READ');
    xarRegisterMask('ReadReferer','All','referer','All','All','ACCESS_READ');
    xarRegisterMask('EditReferer','All','referer','All','All','ACCESS_EDIT');
    xarRegisterMask('AddReferer','All','referer','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteReferer','All','referer','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminReferer','All','referer','All','All','ACCESS_ADMIN');
    xarModSetVar('referer', 'max', '1000');
    xarModSetVar('referer', 'itemsperpage', '100');
    return true;
}
/**
 * upgrade xarbb from an earlier version
 */
function referer_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        // TODO: version numbers - normalise.
        case '1.0.0':
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
             xarDBLoadTableMaintenanceAPI();
            $query = xarDBAlterTable($xartable['referer'],
                              array('command' => 'add',
                                    'field'   => 'xar_time',
                                    'type'    => 'integer',
                                    'unsigned'=> true,
                                    'null'    => false,
                                    'default' => '0'));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;
            break;
    }
    return true;
}

function referer_delete()
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // Drop the table
    $query = "DROP TABLE $xartable[referer]";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    // Remove Masks and Instances
    xarRemoveMasks('referer');
    xarRemoveInstances('referer');
    xarModDelVar('referer', 'max');
    xarModDelVar('referer', 'itemsperpage');
    return true;
}
?>