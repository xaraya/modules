<?php
/**
 * File: $Id$
 *
 * Ping initialization functions
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage ping
 * @author John Cox
 */

// Load Table Maintaince API
sys::import('xaraya.tableddl');

/**
 * initialise the ping module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function ping_init()
{
    // Get database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    // Create tables
    $table = $xartable['ping'];
    $query = xarDBCreateTable($table,
                             array('xar_id'        => array('type'        => 'integer',
                                                             'null'        => false,
                                                             'default'     => '0',
                                                             'increment'   => true,
                                                             'primary_key' => true),
                                   'xar_url'        => array('type'        => 'varchar',
                                                             'size'        => 255,
                                                             'null'        => false,
                                                             'default'     => ''),
                                   'xar_method'    => array('type'        => 'integer',
                                                             'null'        => false,
                                                             'default'     => '0',
                                                             'increment'   => false)));
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    if (!xarModRegisterHook('item', 'update', 'API',
                           'ping', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'create', 'API',
                           'ping', 'admin', 'createhook')) {
        return false;
    }

    $links = array(
        array('http://rpc.weblogs.com/RPC2', 0),
        array('http://api.my.yahoo.com/RPC2', 0),
        array('http://rpc.technorati.com/rpc/ping', 0),
        array('http://rssrpc.weblogs.com/RPC2', 1),
        array('http://ping.blo.gs/', 1));

    foreach ($links as $link){
        // Get next ID in table
        $nextId = $dbconn->GenId($table);
        $query = "INSERT INTO $table (xar_id, xar_url, xar_method) VALUES (?,?,?)";
        $result =& $dbconn->Execute($query,array($nextId, $link[0], (int) $link[1]));
        if (!$result) return;
    }

    xarRegisterMask('Readping', 'All', 'ping', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('Adminping', 'All', 'ping', 'Item', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

/**
 * delete the ping module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function ping_delete()
{

    // need to drop the module tables too
    // Get database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['ping']);
    if (empty($query)) return; // throw back
    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    if (!xarModUnregisterHook('item', 'update', 'API',
                           'ping', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'create', 'API',
                           'ping', 'admin', 'createhook')) {
        return false;
    }

    // Remove Masks and Instances
    xarRemoveMasks('ping');
    xarRemoveInstances('ping');

    // Deletion successful
    return true;
}

/**
 * upgrade the ping module from an old version
 */
function ping_upgrade($oldVersion)
{
    switch($oldVersion) {
    case '1.0.0':
        $modversion['admin']          = 1;
        xarRegisterMask('Adminping', 'All', 'ping', 'Item', 'All:All:All', 'ACCESS_ADMIN');
        if (!xarModRegisterHook('item', 'create', 'API',
                               'ping', 'admin', 'createhook')) {
            return false;
        }
        continue;
        case '1.0.1':
            // Get database information
            $dbconn = xarDB::getConn();
            $xartable = xarDB::getTables();
            // Create tables
            $table = $xartable['ping'];
            $query = xarDBCreateTable($table,
                                     array('xar_id'        => array('type'        => 'integer',
                                                                     'null'        => false,
                                                                     'default'     => '0',
                                                                     'increment'   => true,
                                                                     'primary_key' => true),
                                           'xar_url'        => array('type'        => 'varchar',
                                                                     'size'        => 255,
                                                                     'null'        => false,
                                                                     'default'     => ''),
                                           'xar_method'    => array('type'        => 'integer',
                                                                     'null'        => false,
                                                                     'default'     => '0',
                                                                     'increment'   => false)));
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        continue;
        case '1.0.2':
            // Get database information
            $dbconn = xarDB::getConn();
            $xartable = xarDB::getTables();
            // Create tables
            $table = $xartable['ping'];

            $links = array("'http://rpc.weblogs.com/RPC2', 0",
                           "'http://api.my.yahoo.com/RPC2', 0",
                           "'http://rpc.technorati.com/rpc/ping', 0",
                           "'http://rssrpc.weblogs.com/RPC2', 1",
                           "'http://ping.blo.gs/', 1");
            foreach ($links as $link){
                // Get next ID in table
                $nextId = $dbconn->GenId($table);
                $query = "INSERT INTO $table VALUES ($nextId,$link)";
                $result =& $dbconn->Execute($query);
                if (!$result) return;
            }
       break;
    }
    return true;
}
?>