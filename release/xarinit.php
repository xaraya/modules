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
                                   'xar_time'           => array('type'        => 'integer',
                                                                 'unsigned'    => TRUE,
                                                                 'null'        => false,
                                                                 'default'     => '0'),
                                   'xar_enotes'         => array('type'        => 'text',
                                                                 'default'     => ''),
                                   'xar_type'           => array('type'        => 'varchar',
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
                                   'xar_time'           => array('type'        => 'integer',
                                                                 'unsigned'    => TRUE,
                                                                 'null'        => false,
                                                                 'default'     => '0'),
                                   'xar_approved'       => array('type'        => 'integer',
                                                                 'null'        => false,
                                                                 'default'     => '1',
                                                                 'increment'   => false)));
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // let's hook cats in
    $cid = xarModAPIFunc('categories', 'admin', 'create',
                         array('name' => 'Release',
                               'description' => 'Main Release Cats.',
                               'parent_id' => 0));
    xarModSetVar('release', 'number_of_categories', 1);
    xarModSetVar('release', 'mastercids', $cid);
    xarModSetVar('release', 'SupportShortURLs', 0);

    // Register Block types
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'release',
                'blockType' => 'latest'))) return;
    
    // Enable categories hooks for release
    xarModAPIFunc('modules','admin','enablehooks',
          array('callerModName' => 'release', 'hookModName' => 'categories'));        

    // Register Masks
    xarRegisterMask('OverviewRelease','All','release','All','All','ACCESS_READ');
    xarRegisterMask('EditRelease','All','release','All','All','ACCESS_EDIT');
    xarRegisterMask('DeleteRelease','All','release','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminRelease','All','release','All','All','ACCESS_ADMIN');
    xarRegisterMask('ReadReleaseBlock', 'All', 'release', 'Block', 'All', 'ACCESS_OVERVIEW');

    return true;
}

/**
 * upgrade the release module from an old version
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
        case '0.0.5':
            // let's hook cats in
            $cid = xarModAPIFunc('categories', 'admin', 'create',
                                 array('name' => 'Release',
                                       'description' => 'Main Release Cats.',
                                       'parent_id' => 0));
            xarModSetVar('release', 'number_of_categories', 1);
            xarModSetVar('release', 'mastercids', $cid);
    
            // Enable categories hooks for release
            xarModAPIFunc('modules','admin','enablehooks',
                  array('callerModName' => 'release', 'hookModName' => 'categories'));        
        break;
        case '0.0.6':
            // Set up an initial value for a module variable.
            xarModSetVar('release', 'SupportShortURLs', 0);

            xarRegisterMask('AdminRelease','All','release','All','All','ACCESS_ADMIN');
        break;
        case '0.0.7':
            xarRegisterMask('ReadReleaseBlock', 'All', 'release', 'Block', 'All', 'ACCESS_OVERVIEW');
            // Register Block types
            if (!xarModAPIFunc('blocks',
                    'admin',
                    'register_block_type',
                    array('modName' => 'release',
                        'blockType' => 'latest'))) return;
        break;
        case '0.0.8':
            xarRegisterMask('ReadRelease', 'All', 'release', 'All', 'All', 'ACCESS_READ');

            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();

            $releasetable = $xartable['release_notes'];
            // FIXME: non-portable SQL
            $query = "select xar_rnid,xar_time from $releasetable";
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // FIXME: non-portable SQL
            $query = "ALTER TABLE $releasetable
                         CHANGE xar_time xar_time INT UNSIGNED DEFAULT '0' NOT NULL";
            $altresult =& $dbconn->Execute($query);
            if (!$altresult) return;
            $altresult->Close();

            while (!$result->EOF) {
                $newtime = strtotime($result->fields[1]);
                $newid = $result->fields[0];

                $query = "update $releasetable set xar_time=$newtime where xar_rnid=$newid";
                $result2 =& $dbconn->Execute($query);
                if (!$result2) return;
                $result2->Close();

                $result->MoveNext();
            }
            $result->Close();

            $releasetable = $xartable['release_docs'];
            // FIXME: non-portable SQL
            $query = "select xar_rdid,xar_time from $releasetable";
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // FIXME: non-portable SQL
            $query = "ALTER TABLE $releasetable
                         CHANGE xar_time xar_time INT UNSIGNED DEFAULT '0' NOT NULL";
            $altresult =& $dbconn->Execute($query);
            if (!$altresult) return;
            $altresult->Close();

            while (!$result->EOF) {
                $newtime = strtotime($result->fields[1]);
                $newid = $result->fields[0];

                $query = "update $releasetable set xar_time=$newtime where xar_rdid=$newid";
                $result2 =& $dbconn->Execute($query);
                if (!$result2) return;
                $result2->Close();

                $result->MoveNext();
            }
            $result->Close();
        break;
        case '0.0.9':
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

    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'release',
                'blockType' => 'latest'))) return;

    // UnRegister Masks
    xarUnRegisterMask('OverviewRelease');
    xarUnRegisterMask('EditRelease');
    xarUnRegisterMask('DeleteRelease');
    xarUnRegisterMask('ReadReleaseBlock');

    return true;
}
?>