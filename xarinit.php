<?php
/**
 * Release initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
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
                                                                  'increment'   => false),
                                    'xar_usefeed'       => array('type'         => 'integer',
                                                                 'size'         => 'tiny',
                                                                 'null'         => false,
                                                                 'default'      => '1',
                                                                 'increment'    => false)
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
        case '0.1.0':
            xarModAPIFunc('modules','admin','add_module_alias', array(
                'modName' => 'release',
                'aliasModName' => 'rid'));
        case '0.1.1':
           // search hook
           if (!xarModRegisterHook('item', 'search', 'GUI', 'release', 'user', 'search')) {
               return false;
           }
        case '0.1.2':
          $dbconn =& xarDBGetConn();
          $xartable =& xarDBGetTables();
          $releasenotes = $xartable['release_notes'];

          xarDBLoadTableMaintenanceAPI();
          $query = xarDBAlterTable($releasenotes,
                             array('command'  => 'add',
                                    'field'   => 'xar_usefeed',
                                    'type'    => 'integer',
                                    'null'    =>  false,
                                    'size'    => 'tiny',
                                    'default' => '1'));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;
            // fall through to next upgrade
           //now populate the existing release notes with the default of 1

           $query= "SELECT COUNT(1)
                    FROM $releasenotes";
           $result =& $dbconn->Execute($query);
           if (!$result) return;

           for (; !$result->EOF; $result->MoveNext()) {
               $updateusefeed = "UPDATE $releasenotes
                                 SET xar_usefeed    = 1";
               $doupdate =& $dbconn->Execute($updateusefeed);
               if (!$doupdate) return;
           }
        case '0.2.0': //current version

        break;
    }

    /* Update successful */
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

     // Delete any module variables
    xarModDelAllVars('release');
    // Remove Masks and Instances
    xarRemoveMasks('release');
    xarRemoveInstances('release');


    return true;
}
?>
