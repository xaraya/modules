<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: John Cox via phpMailer Team 
// Purpose of file:  Initialisation functions for the Mail Hook
// ----------------------------------------------------------------------

//Load Table Maintainance API

xarDBLoadTableMaintenanceAPI();

/**
 * initialise the send to friend module
 */
function release_init()
{
    // Set up database tables
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // *_user_data
    $query = xarDBCreateTable($releasetable,
                             array('xar_rid'         => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '0',
                                                              'increment'   => true,
                                                              'primary_key' => true),
                                   'xar_uid'         => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '0',
                                                              'increment'   => false),
                                   'xar_name'        => array('type'        => 'varchar',
                                                              'size'        => 100,
                                                              'null'        => false,
                                                              'default'     => ''),
                                   'xar_desc'        => array('type'        => 'text',
                                                              'default'     => ''),
                                   'xar_type'        => array('type'        => 'varchar',
                                                              'size'        => 100,
                                                              'null'        => false,
                                                              'default'     => ''),
                                   'xar_approved'    => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '0',
                                                              'increment'   => false)));
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_xar_release_id_1',
                   'fields'    => array('xar_name'),
                   'unique'    => TRUE);
    $query = xarDBCreateIndex($releasetable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $releasenotes = $xartable['release_notes'];

    // *_user_data
    $query = xarDBCreateTable($releasenotes,
                             array('xar_rnid'           => array('type'        => 'integer',
                                                                'null'         => false,
                                                                'default'      => '0',
                                                                'increment'    => true,
                                                                'primary_key'  => true),
                                   'xar_rid'            => array('type'        => 'integer',
                                                                 'null'        => false,
                                                                 'default'     => '0',
                                                                 'increment'   => false),
                                   'xar_version'        => array('type'        => 'varchar',
                                                                 'size'        => 100,
                                                                 'null'        => false,
                                                                 'default'     => ''),
                                   'xar_price'          => array('type'        => 'integer',
                                                                 'null'        => false,
                                                                 'default'     => '0',
                                                                 'increment'   => false),
                                   'xar_priceterms'     => array('type'        => 'varchar',
                                                                 'size'        => 100,
                                                                 'null'        => false,
                                                                 'default'     => ''),
                                   'xar_demo'           => array('type'        => 'integer',
                                                                 'null'        => false,
                                                                 'default'     => '0',
                                                                 'increment'   => false),
                                   'xar_demolink'       => array('type'        => 'varchar',
                                                                 'size'        => 100,
                                                                 'null'        => false,
                                                                 'default'     => ''),
                                   'xar_supported'      => array('type'        => 'integer',
                                                                 'null'        => false,
                                                                 'default'     => '0',
                                                                 'increment'   => false),
                                   'xar_supportlink'    => array('type'        => 'varchar',
                                                                 'size'        => 100,
                                                                 'null'        => false,
                                                                 'default'     => ''),
                                   'xar_certified'      => array('type'        => 'integer',
                                                                 'null'        => false,
                                                                 'default'     => '0',
                                                                 'increment'   => false),
                                   'xar_approved'       => array('type'        => 'integer',
                                                                 'null'        => false,
                                                                 'default'     => '0',
                                                                 'increment'   => false)));
    $result =& $dbconn->Execute($query);
    if (!$result) return;

/*
    $releasedocs = $xartable['release_docs'];

    // *_user_data
    $query = xarDBCreateTable($releasedocs,
                             array('xar_rid'         => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '0',
                                                              'increment'   => true,
                                                              'primary_key' => true),
                                   'xar_uid'         => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '0',
                                                              'increment'   => false),
                                   'xar_name'        => array('type'        => 'varchar',
                                                              'size'        => 100,
                                                              'null'        => false,
                                                              'default'     => ''),
                                   'xar_desc'        => array('type'        => 'text',
                                                              'default'     => ''),
                                   'xar_type'        => array('type'        => 'varchar',
                                                              'size'        => 100,
                                                              'null'        => false,
                                                              'default'     => ''),
                                   'xar_approved'    => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '0',
                                                              'increment'   => false)));
    $result =& $dbconn->Execute($query);
    if (!$result) return;
*/
    return true;
}

/**
 * upgrade the example module from an old version
 * This function can be called multiple times
 */
function release_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case 0.02:

            list($dbconn) = xarDBGetConn();
            $xartable = xarDBGetTables();
            $releaseidtable = $xartable['release_id'];

            // Add a column to the table

            xarDBLoadTableMaintenanceAPI();

            $query = xarDBAlterTable(array('table' => $releaseidtable,
                                           'command' => 'add',
                                           'field' => 'xar_uid',
                                           'type' => 'integer',
                                           'null' => false,
                                           'default' => '0'));

            // Pass to ADODB, and send exception if the result isn't valid.
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            return example_upgrade(0.02);

    }
}
/**
 * delete the send to friend module
 */
function release_delete()
{

    // Set up database tables
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // Drop the table
    $query = "DROP TABLE $xartable[release_id]";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Drop the table
    $query = "DROP TABLE $xartable[release_notes]";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    return true;
}
?>