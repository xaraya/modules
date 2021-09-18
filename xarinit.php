<?php
/**
 * Site Tools Initialization
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools Module
 * @link http://xaraya.com/index.php/release/887.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * initialise the sitetools module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function sitetools_init()
{
    /* Get datbase setup - note that both xarDB::getConn() and xarDB::getTables()
     * return arrays but we handle them differently.
     */
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $sitetoolstable = $xartable['sitetools'];

    sys::import('xaraya.tableddl');
    /* Define the table structure in this associative array
     * There is one element for each field.  The key for the element is
     * the physical field name.  The element contains another array specifying the
     * data type and associated parameters
     */
    $fields = ['xar_stid' => ['type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true],
                    'xar_stgained' => ['type'=>'float', 'size' =>'decimal', 'width'=>12, 'decimals'=>2],
                ];

    $query = xarTableDDL::createTable($sitetoolstable, $fields);
    if (empty($query)) {
        return;
    } // throw back

    /* Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful */
    $result = &$dbconn->Execute($query);
    if (!$result) {
        return;
    }

    $linkstable = $xartable['sitetools_links'];
    $query = xarTableDDL::createTable(
        $linkstable,
        ['xar_id'         => ['type'        => 'integer',
                                                            'null'       => false,
                                                            'increment'  => true,
                                                            'primary_key' => true, ],
                                   'xar_link'       => ['type'        => 'varchar',
                                                            'size'        => 254,
                                                            'null'        => false,
                                                            'default'     => '', ],
                                   'xar_status'     => ['type'        => 'integer',
                                                            'null'        => false,
                                                            'default'     => '0', ],
                                   'xar_moduleid'   => ['type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0', ],
                                   'xar_itemtype'   => ['type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0', ],
                                   'xar_itemid'     => ['type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0', ],
                                   'xar_itemtitle'  => ['type'        => 'varchar',
                                                            'size'        => 254,
                                                            'null'        => false,
                                                            'default'     => '', ],
                                   'xar_itemlink'   => ['type'        => 'varchar',
                                                            'size'        => 254,
                                                            'null'        => false,
                                                            'default'     => '', ],
                                  ]
    );

    if (empty($query)) {
        return;
    }

    /* Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful */
    $result = &$dbconn->Execute($query);
    if (!$result) {
        return;
    }

    /* allow several entries for the same link here */
    $index = [
        'name'      => 'i_' . xarDB::getPrefix() . '_sitetools_links_link',
        'fields'    => ['xar_link'],
        'unique'    => false,
    ];
    $query = xarTableDDL::createIndex($linkstable, $index);
    $result =& $dbconn->Execute($query);
    if (!$result) {
        return;
    }

    /* allow several links for the same module item */
    $index = [
        'name'      => 'i_' . xarDB::getPrefix() . '_sitetools_links_combo',
        'fields'    => ['xar_moduleid','xar_itemtype','xar_itemid'],
        'unique'    => false,
    ];
    $query = xarTableDDL::createIndex($linkstable, $index);
    $result =& $dbconn->Execute($query);
    if (!$result) {
        return;
    }

    /* allow many entries with the same status here */
    $index = [
        'name'      => 'i_' . xarDB::getPrefix() . '_sitetools_links_status',
        'fields'    => ['xar_status'],
        'unique'    => false,
    ];
    $query = xarTableDDL::createIndex($linkstable, $index);
    $result =& $dbconn->Execute($query);
    if (!$result) {
        return;
    }

    /* create the dynamic object that will represent our items */
    $objectid = xarMod::apiFunc(
        'dynamicdata',
        'util',
        'import',
        ['file' => sys::code() . 'modules/sitetools/sitetools_links.xml']
    );
    if (empty($objectid)) {
        return;
    }
    // save the object id for later
    xarModVars::set('sitetools', 'objectid_links', $objectid);

    /* Set up an initial value for a module variable. */
    /* Use relative path for now */
    /*   if( isset( $_SERVER['PATH_TRANSLATED'] ) )
    {
        $backupdir = dirname(realpath($_SERVER['PATH_TRANSLATED'])) . '/var/uploads/backup';
    } elseif( isset( $_SERVER['SCRIPT_FILENAME'] ) ) {
        $backupdir = dirname(realpath($_SERVER['SCRIPT_FILENAME'])) . '/var/uploads/backup';
    } else {
        $backupdir = 'var/uploads/backup';
    }
    */
    $backupdir=sys::varpath()."/uploads";
    xarModVars::set('sitetools', 'adocachepath', sys::varpath()."/cache/adodb");
    xarModVars::set('sitetools', 'rsscachepath', sys::varpath()."/cache/rss");
    xarModVars::set('sitetools', 'templcachepath', sys::varpath()."/cache/templates");
    xarModVars::set('sitetools', 'backuppath', $backupdir);
    xarModVars::set('sitetools', 'lineterm', '\n');
    xarModVars::set('sitetools', 'timestamp', 1);
    xarModVars::set('sitetools', 'colnumber', 3);
    xarModVars::set('sitetools', 'defaultbktype', 'complete');
    xarModVars::set('sitetools', 'links_skiplocal', 1);
    xarModVars::set('sitetools', 'links_method', 'GET');
    xarModVars::set('sitetools', 'links_follow', 0);
    /**
     * Register the module components that are privileges objects
     * Format is
     * xarMasks::register(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarMasks::register('ReadSiteToolsBlock', 'All', 'sitetools', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarMasks::register('ViewSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarMasks::register('ReadSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_READ');
    xarMasks::register('EditSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarMasks::register('AddSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarMasks::register('DeleteSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarMasks::register('AdminSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

/**
 * upgrade the example module from an old version
 * This function can be called multiple times
 */
function sitetools_upgrade($oldversion)
{
    /* Upgrade dependent on old version number */
    switch ($oldversion) {
        case 0.1:

            $dbconn = xarDB::getConn();
            $xartable = xarDB::getTables();

            sys::import('xaraya.tableddl');

            $linkstable = $xartable['sitetools_links'];
            $query = xarTableDDL::createTable(
                $linkstable,
                ['xar_id'         => ['type'        => 'integer',
                                                                    'null'       => false,
                                                                    'increment'  => true,
                                                                    'primary_key' => true, ],
                                           'xar_link'       => ['type'        => 'varchar',
                                                                    'size'        => 254,
                                                                    'null'        => false,
                                                                    'default'     => '', ],
                                           'xar_status'     => ['type'        => 'integer',
                                                                    'null'        => false,
                                                                    'default'     => '0', ],
        /* TODO: replace with unique id*/
                                           'xar_moduleid'   => ['type'        => 'integer',
                                                                    'unsigned'    => true,
                                                                    'null'        => false,
                                                                    'default'     => '0', ],
                                           'xar_itemtype'   => ['type'        => 'integer',
                                                                    'unsigned'    => true,
                                                                    'null'        => false,
                                                                    'default'     => '0', ],
                                           'xar_itemid'     => ['type'        => 'integer',
                                                                    'unsigned'    => true,
                                                                    'null'        => false,
                                                                    'default'     => '0', ],
                                           'xar_itemtitle'  => ['type'        => 'varchar',
                                                                    'size'        => 254,
                                                                    'null'        => false,
                                                                    'default'     => '', ],
                                           'xar_itemlink'   => ['type'        => 'varchar',
                                                                    'size'        => 254,
                                                                    'null'        => false,
                                                                    'default'     => '', ],
                                          ]
            );

            if (empty($query)) {
                return;
            } // throw back

            /* Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful */
            $result = &$dbconn->Execute($query);
            if (!$result) {
                return;
            }

            /* allow several entries for the same link here */
            $index = [
                'name'      => 'i_' . xarDB::getPrefix() . '_sitetools_links_link',
                'fields'    => ['xar_link'],
                'unique'    => false,
            ];
            $query = xarTableDDL::createIndex($linkstable, $index);
            $result =& $dbconn->Execute($query);
            if (!$result) {
                return;
            }

            /* allow several links for the same module item */
            $index = [
                'name'      => 'i_' . xarDB::getPrefix() . '_sitetools_links_combo',
                'fields'    => ['xar_moduleid','xar_itemtype','xar_itemid'],
                'unique'    => false,
            ];
            $query = xarTableDDL::createIndex($linkstable, $index);
            $result =& $dbconn->Execute($query);
            if (!$result) {
                return;
            }

            /* allow many entries with the same status here */
            $index = [
                'name'      => 'i_' . xarDB::getPrefix() . '_sitetools_links_status',
                'fields'    => ['xar_status'],
                'unique'    => false,
            ];
            $query = xarTableDDL::createIndex($linkstable, $index);
            $result =& $dbconn->Execute($query);
            if (!$result) {
                return;
            }

            /* create the dynamic object that will represent our items */
            $objectid = xarMod::apiFunc(
                'dynamicdata',
                'util',
                'import',
                ['file' => 'modules/sitetools/sitetools_links.xml']
            );
            if (empty($objectid)) {
                return;
            }
            /* save the object id for later */
            xarModVars::set('sitetools', 'objectid_links', $objectid);
            /*update vars for backup tool*/
            xarModVars::set('sitetools', 'colnumber', 3);
            xarModVars::set('sitetools', 'defaultbktype', 'complete');
            // no break
        case '0.2':
        case 1.0:

        case 2.0:
    }
    /* Update successful */
    return true;
}

/**
 * delete the example module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function sitetools_delete()
{
    /* Get datbase setup */
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    sys::import('xaraya.tableddl');
    /* Generate the SQL to drop the table using the API */
    $query = xarTableDDL::dropTable($xartable['sitetools']);
    if (empty($query)) {
        return;
    }

    /* Drop the table and send exception if returns false. */
    $result = &$dbconn->Execute($query);
    if (!$result) {
        return;
    }

    /* delete the dynamic object and its properties */
    $objectid = xarModVars::get('sitetools', 'objectid_links');
    if (!empty($objectid)) {
        xarMod::apiFunc(
            'dynamicdata',
            'admin',
            'deleteobject',
            ['objectid' => $objectid]
        );
        xarModVars::delete('sitetools', 'objectid_links');
    }

    /* Generate the SQL to drop the table using the API */
    $query = xarTableDDL::dropTable($xartable['sitetools_links']);
    if (empty($query)) {
        return;
    }
    /* Drop the table and send exception if returns false. */
    $result = &$dbconn->Execute($query);
    if (!$result) {
        return;
    }

    /* Delete any sitetools module variables */
    xarModVars::delete_all('sitetools');

    /* Remove Masks and Instances */
    xarMasks::removemasks('sitetools');

    /* Deletion successful */
    return true;
}
