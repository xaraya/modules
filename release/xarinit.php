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
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

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
                                   'xar_certified'   => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '1',
                                                              'increment'   => false),
                                   'xar_approved'    => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '0',
                                                              'increment'   => false)));
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_'.xarDBGetSiteTablePrefix().'_release_id_1',
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
                                                                 'default'     => '1',
                                                                 'increment'   => false),
                                   'xar_priceterms'     => array('type'        => 'varchar',
                                                                 'size'        => 100,
                                                                 'null'        => false,
                                                                 'default'     => ''),
                                   'xar_demo'           => array('type'        => 'integer',
                                                                 'null'        => false,
                                                                 'default'     => '1',
                                                                 'increment'   => false),
                                   'xar_demolink'       => array('type'        => 'varchar',
                                                                 'size'        => 100,
                                                                 'null'        => false,
                                                                 'default'     => ''),
                                   'xar_dllink'         => array('type'        => 'varchar',
                                                                 'size'        => 100,
                                                                 'null'        => false,
                                                                 'default'     => ''),
                                   'xar_supported'      => array('type'        => 'integer',
                                                                 'null'        => false,
                                                                 'default'     => '1',
                                                                 'increment'   => false),
                                   'xar_supportlink'    => array('type'        => 'varchar',
                                                                 'size'        => 100,
                                                                 'null'        => false,
                                                                 'default'     => ''),
                                   'xar_changelog'      => array('type'        => 'text',
                                                                 'default'     => ''),
                                   'xar_notes'          => array('type'        => 'text',
                                                                 'default'     => ''),
                                   'xar_time'           => array('type'        => 'datetime',
                                                                 'null'        => false,
                                                                 'default'     => '0000-00-00 00:00:00'),
                                   'xar_enotes'         => array('type'        => 'text',
                                                                 'default'     => ''),
                                       'xar_type'        => array('type'        => 'varchar',
                                                                  'size'        => 100,
                                                                  'null'        => false,
                                                                  'default'     => ''),
                                   'xar_certified'      => array('type'        => 'integer',
                                                                 'null'        => false,
                                                                 'default'     => '1',
                                                                 'increment'   => false),
                                   'xar_approved'       => array('type'        => 'integer',
                                                                 'null'        => false,
                                                                 'default'     => '0',
                                                                 'increment'   => false)));
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $releasedocs = $xartable['release_docs'];

    // *_user_data
    $query = xarDBCreateTable($releasedocs,
                             array('xar_rdid'           => array('type'        => 'integer',
                                                                 'null'        => false,
                                                                 'default'     => '0',
                                                                 'increment'   => true,
                                                                 'primary_key' => true),
                                   'xar_rid'            => array('type'        => 'integer',
                                                                 'null'        => false,
                                                                 'default'     => '0',
                                                                 'increment'   => false),
                                   'xar_title'          => array('type'        => 'varchar',
                                                                 'size'        => 100,
                                                                 'null'        => false,
                                                                 'default'     => ''),
                                   'xar_docs'           => array('type'        => 'text',
                                                                 'default'     => ''),
                                   'xar_type'           => array('type'        => 'varchar',
                                                                 'size'        => 100,
                                                                 'null'        => false,
                                                                 'default'     => ''),
                                   'xar_time'           => array('type'        => 'datetime',
                                                                 'null'        => false,
                                                                 'default'     => '0000-00-00 00:00:00'),
                                   'xar_approved'       => array('type'        => 'integer',
                                                                 'null'        => false,
                                                                 'default'     => '1',
                                                                 'increment'   => false)));
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Register Masks
    xarRegisterMask('OverviewRelease','All','release','All','All','ACCESS_READ');
    xarRegisterMask('EditRelease','All','release','All','All','ACCESS_EDIT');
    xarRegisterMask('DeleteRelease','All','release','All','All','ACCESS_DELETE');

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
        case 0.04:

            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $releaseidtable = $xartable['release_notes'];

            // Add a column to the table

            xarDBLoadTableMaintenanceAPI();

            $query = xarDBAlterTable($releaseidtable,
                array(
                                           'command' => 'add',
                                           'field' => 'xar_type',
                                           'type'  => 'varchar',
                                           'size'        => 100,
                                           'null'        => false,
                                           'default'     => 'module'));

            // Pass to ADODB, and send exception if the result isn't valid.
            $result =& $dbconn->Execute($query);
            if (!$result) return;

        break;
        case '0.05':

    }

    return true;
}
/**
 * delete the send to friend module
 */
function release_delete()
{

    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // Drop the table
    $query = "DROP TABLE $xartable[release_id]";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Drop the table
    $query = "DROP TABLE $xartable[release_notes]";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Drop the table
    $query = "DROP TABLE $xartable[release_docs]";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // UnRegister Masks
    xarUnRegisterMask('OverviewRelease');
    xarUnRegisterMask('EditRelease');
    xarUnRegisterMask('DeleteRelease');

    return true;
}
?>