<?php
/**
 * Release initialization functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 */
/**
 * initialization functions
 * Initialise the Release module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 * @return bool
 */

function release_init()
{
    xarDBLoadTableMaintenanceAPI();
    
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
                                   'xar_regname'     => array('type'        => 'varchar',
                                                              'size'        => 100,
                                                              'null'        => false,
                                                              'default'     => ''),
                                   'xar_displname'   => array('type'        => 'varchar',
                                                              'size'        => 200,
                                                              'null'        => false,
                                                              'default'     => ''),
                                   'xar_desc'        => array('type'        => 'text'),
                                   'xar_type'        => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '0',
                                                              'increment'   => false),
                                   'xar_class'       => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '0',
                                                              'increment'   => false),
                                   'xar_certified'   => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '1',
                                                              'increment'   => false),
                                   'xar_approved'    => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '0',
                                                              'increment'   => false),
                                   'xar_rstate'      => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '0',
                                                              'increment'   => false)
                                                              ));
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_'.xarDBGetSiteTablePrefix().'_release_id_1',
                   'fields'    => array('xar_regname','xar_type'),
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
                                   'xar_changelog'      => array('type'        => 'text'),
                                   'xar_notes'          => array('type'        => 'text'),
                                   'xar_time'           => array('type'        => 'integer',
                                                                 'unsigned'    => TRUE,
                                                                 'null'        => false,
                                                                 'default'     => '0'),
                                   'xar_enotes'         => array('type'        => 'text'),
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
                                                                 'increment'   => false),
                                    'xar_rstate'        =>  array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '0',
                                                              'increment'   => false)
                                                                 ));
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
                                   'xar_docs'           => array('type'        => 'text'),
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
                       array('modName'   => 'release',
                             'blockType' => 'latest'))) return;
    
    // Enable categories hooks for release
    xarModAPIFunc('modules','admin','enablehooks',
          array('callerModName' => 'release', 'hookModName' => 'categories'));        
    // search hook
    if (!xarModRegisterHook('item', 'search', 'GUI', 'release', 'user', 'search')) {
        return false;
    }
    // Register Masks
    xarRegisterMask('OverviewRelease','All','release','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadRelease', 'All', 'release', 'All', 'All', 'ACCESS_READ');
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

            return release_upgrade('0.0.5');
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
            return release_upgrade('0.0.6');
        case '0.0.6':
            // Set up an initial value for a module variable.
            xarModSetVar('release', 'SupportShortURLs', 0);

            xarRegisterMask('AdminRelease','All','release','All','All','ACCESS_ADMIN');
            return release_upgrade('0.0.7');
        case '0.0.7':
            xarRegisterMask('ReadReleaseBlock', 'All', 'release', 'Block', 'All', 'ACCESS_OVERVIEW');
            // Register Block types
            if (!xarModAPIFunc('blocks',
                    'admin',
                    'register_block_type',
                    array('modName' => 'release',
                        'blockType' => 'latest'))) return;
            return release_upgrade('0.0.8');
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
            return release_upgrade('0.0.9');
        case '0.0.9':
            xarRegisterMask('OverviewRelease','All','release','All','All','ACCESS_OVERVIEW');
            xarRegisterMask('ReadRelease', 'All', 'release', 'All', 'All', 'ACCESS_READ');
            xarRegisterMask('EditRelease','All','release','All','All','ACCESS_EDIT');
            xarRegisterMask('DeleteRelease','All','release','All','All','ACCESS_DELETE');
            xarRegisterMask('AdminRelease','All','release','All','All','ACCESS_ADMIN');
            xarRegisterMask('ReadReleaseBlock', 'All', 'release', 'Block', 'All', 'ACCESS_OVERVIEW');
            return release_upgrade('0.0.10');
        case '0.0.10':
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $releasetable = $xartable['release_id'];
            $releasenotes = $xartable['release_notes'];
           //Add new rstate field to release table
            $query = xarDBAlterTable($releasetable,
                              array('command' => 'add',
                                    'field'   => 'xar_rstate',
                                    'type'    => 'integer',
                                    'unsigned'=> true,
                                    'null'    => false,
                                    'default' => '0'));
            // Pass to ADODB, and send exception if the result isn't valid.

            $result = &$dbconn->Execute($query);
            if (!$result) return;
           //Now add new rstate field to release notes table -sigh
            $query = xarDBAlterTable($releasenotes,
                              array('command' => 'add',
                                    'field'   => 'xar_rstate',
                                    'type'    => 'integer',
                                    'unsigned'=> true,
                                    'null'    => false,
                                    'default' => '0'));
            // Pass to ADODB, and send exception if the result isn't valid.

            $result = &$dbconn->Execute($query);
            if (!$result) return;

            return release_upgrade('0.0.11');
        case '0.0.11':
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $releasetable = $xartable['release_id'];
            //Add new class field to release table
            $query = xarDBAlterTable($releasetable,
                              array('command' => 'add',
                                    'field'   => 'xar_class',
                                    'type'    => 'integer',
                                    'unsigned'=> true,
                                    'null'    => false,
                                    'default' => '0'));
            // Pass to ADODB, and send exception if the result isn't valid.

            $result = &$dbconn->Execute($query);
            if (!$result) return;

            //Add new dispname field to release table
            $query = xarDBAlterTable($releasetable,
                              array('command' => 'add',
                                    'field'   => 'xar_displname',
                                    'type'    => 'varchar',
                                    'size'    => 200,
                                    'null'    => false,
                                    'default' => ''));

            // Pass to ADODB, and send exception if the result isn't valid.

            $result = &$dbconn->Execute($query);
            if (!$result) return;

            // FIXME: non-portable SQL
            $query = "update $releasetable set xar_type='0' where xar_type='Module'";
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            $result->Close();

            // FIXME: non-portable SQL
            $query = "update $releasetable set xar_type='1' where xar_type='Theme'";
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            $result->Close();

            // FIXME: non-portable SQL
            $query = "update $releasetable set xar_name='skribetheme' where xar_rid='5555'";
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            $result->Close();

            // FIXME: non-portable SQL
            $query = "update $releasetable set xar_name='ComputerPoint' where xar_rid='2001'";
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            $result->Close();

            // FIXME: non-portable SQL
            $query = "ALTER TABLE $releasetable
                         CHANGE xar_type xar_type INT UNSIGNED DEFAULT '0' NOT NULL";
            $altresult =& $dbconn->Execute($query);
            if (!$altresult) return;
            $altresult->Close();

            $index = array('name'      => 'i_'.xarDBGetSiteTablePrefix().'_release_id_1',
                           'fields'    => array('xar_name'),
                           'unique'    => TRUE);
            $query = xarDBDropIndex($releasetable,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // FIXME: non-portable SQL
            $query = "ALTER TABLE $releasetable
                         CHANGE xar_name xar_regname varchar(100) NOT NULL default ''";
            $altresult =& $dbconn->Execute($query);
            if (!$altresult) return;
            $altresult->Close();

            // FIXME: non-portable SQL
            $query = "update $releasetable set xar_class='1'";
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            $result->Close();

            // FIXME: non-portable SQL
            $query = "select xar_rid,xar_regname from $releasetable";
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            while (!$result->EOF) {
                $newid = $result->fields[0];
                $newrn = strtolower($result->fields[1]);
                $newdn = $result->fields[1];

                $query = "update $releasetable set xar_regname='$newrn',xar_displname='$newdn' where xar_rid=$newid";
                $result2 =& $dbconn->Execute($query);
                if (!$result2) return;
                $result2->Close();

                $result->MoveNext();
            }
            $result->Close();

            $index = array('name'      => 'i_'.xarDBGetSiteTablePrefix().'_release_id_1',
                           'fields'    => array('xar_regname','xar_type'),
                           'unique'    => TRUE);
            $query = xarDBCreateIndex($releasetable,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            return release_upgrade('0.0.12');
        case '0.0.12':

        if (!xarModRegisterHook('item', 'waitingcontent', 'GUI',
                           'release', 'admin', 'waitingcontent')) {
            return false;
        }
        break;
        case '0.1.0':
            xarModAPIFunc('modules','admin','add_module_alias', array(
                'modName' => 'release',
                'aliasModName' => 'rid'));
            return true;
        break;
        case '0.1.1':
           // search hook
           if (!xarModRegisterHook('item', 'search', 'GUI', 'release', 'user', 'search')) {
               return false;
           }
        break; //fall thru

        case '0.1.2':
        return false;
    }

    return true;
}
/**
 * delete the release module
 * @return bool
 */
function release_delete()
{

    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Drop the release_id table
    $query = "DROP TABLE $xartable[release_id]";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Drop the release_notes table
    $query = "DROP TABLE $xartable[release_notes]";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Drop the release_docs table
    $query = "DROP TABLE $xartable[release_docs]";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                        array('modName'   => 'release',
                              'blockType' => 'latest'))) return;

    // Disable categories hooks for release
       xarModAPIFunc('modules','admin','disablehooks',
          array('callerModName' => 'release', 'hookModName' => 'categories'));

    /* jojodee: this is a problem as categories are used by other modules on the site
     // Delete cats
    if (!xarModAPIFunc('categories', 'admin', 'deletecat',
                         array('cid' => xarModGetVar('release', 'mastercids')))) return;
    */
    // Delete any module variables
    xarModDelAllVars('release');
    // Remove Masks and Instances
    xarRemoveMasks('release');
    xarRemoveInstances('release');


    return true;
}
?>
