<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
 * Initialise the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance. It holds all the installation routines and sets the variables used
 * by this module.
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @param none
 * @return bool true on success of installation
 */
function crispbb_init()
{
    $module = 'crispbb';

    # --------------------------------------------------------
#
    # Create tables
#
    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $prefix = xarDB::getPrefix();
    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    $itemtypestable = $tables['crispbb_itemtypes'];
    $forumstable    = $tables['crispbb_forums'];
    $topicstable    = $tables['crispbb_topics'];
    $poststable     = $tables['crispbb_posts'];
    $hookstable     = $tables['crispbb_hooks'];
    $posterstable   = $tables['crispbb_posters'];



    // Create tables inside a transaction
    try {
        $charset = xarSystemVars::get(sys::CONFIG, 'DB.Charset');
        $dbconn->begin();
        sys::import('xaraya.structures.query');
        $q = new Query();
        // forums table
        $query = "DROP TABLE IF EXISTS " . $forumstable;
        if (!$q->run($query)) {
            return;
        }
        $fields = [
            'id' => ['type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true],
            'fstatus' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'fowner' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'forder' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'lasttid' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'ftype' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'numtopics' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'numreplies' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'numtopicsubs' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'numreplysubs' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'numtopicdels' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'numreplydels' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'fname' => ['type' => 'varchar','size' => 100,'null' => false, 'charset' => $charset],
            'fdesc' => ['type' => 'varchar','size' => 255,'null' => false, 'charset' => $charset],
            'fsettings' => ['type' => 'text', 'charset' => $charset],
            'fprivileges' => ['type' => 'text', 'charset' => $charset],
        ];
        $query = xarTableDDL::createTable($forumstable, $fields);
        $dbconn->Execute($query);

        // fstatus
        $index = ['name' => $prefix . '_crispbb_forums_fstatus',
                       'fields' => ['fstatus'],
                       ];
        $query = xarTableDDL::createIndex($forumstable, $index);
        $dbconn->Execute($query);

        // forder
        $index = ['name' => $prefix . '_crispbb_forums_forder',
                       'fields' => ['forder'],
                       ];
        $query = xarTableDDL::createIndex($forumstable, $index);
        $dbconn->Execute($query);

        // fname
        $index = ['name' => $prefix . '_crispbb_forums_fname',
                       'fields' => ['fname'],
                       ];
        $query = xarTableDDL::createIndex($forumstable, $index);
        $dbconn->Execute($query);

        // itemtypes table
        $query = "DROP TABLE IF EXISTS " . $itemtypestable;
        if (!$q->run($query)) {
            return;
        }
        $fields = [
            'id' => ['type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true],
            'fid' => ['type' => 'integer', 'unsigned' => true, 'null' => false],
            'component' => ['type' => 'varchar','size' => 10,'null' => false, 'charset' => $charset],
        ];
        $query = xarTableDDL::createTable($itemtypestable, $fields);
        $dbconn->Execute($query);

        // itemtypes, every entry must be unique
        $index = ['name' => $prefix . '_crispbb_itemtypes_combo',
                       'fields' => ['fid', 'component'],
                       'unique' => true,
                       ];
        $query = xarTableDDL::createIndex($itemtypestable, $index);
        $dbconn->Execute($query);

        // topics table
        $query = "DROP TABLE IF EXISTS " . $topicstable;
        if (!$q->run($query)) {
            return;
        }
        $fields = [
            'id' => ['type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true],
            'fid' => ['type' => 'integer', 'unsigned' => true, 'null' => false],
            'ttype' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'tstatus' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'towner' => ['type' => 'integer', 'unsigned' => true, 'null' => false],
            'topicstype' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'firstpid' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'lastpid' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'numreplies' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'numsubs' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'numdels' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'ttitle' => ['type' => 'varchar','size' => 255,'null' => false, 'charset' => $charset],
            'tsettings' => ['type' => 'text', 'charset' => $charset],
        ];
        $query = xarTableDDL::createTable($topicstable, $fields);
        $dbconn->Execute($query);

        // fid
        $index = ['name' => $prefix . '_crispbb_topics_fid',
                       'fields' => ['fid'],
                       ];
        $query = xarTableDDL::createIndex($topicstable, $index);
        $dbconn->Execute($query);

        // ttype
        $index = ['name' => $prefix . '_crispbb_topics_ttype',
                       'fields' => ['ttype'],
                       ];
        $query = xarTableDDL::createIndex($topicstable, $index);
        $dbconn->Execute($query);

        // tstatus
        $index = ['name' => $prefix . '_crispbb_topics_tstatus',
                       'fields' => ['tstatus'],
                       ];
        $query = xarTableDDL::createIndex($topicstable, $index);
        $dbconn->Execute($query);

        // towner
        /*
        $index = array('name' => $prefix . '_crispbb_topics_towner',
                       'fields' => array('towner')
                       );
        $query = xarTableDDL::createIndex($topicstable, $index);
        $dbconn->Execute($query);
        */
        // ttitle
        $index = ['name' => $prefix . '_crispbb_topics_ttitle',
                       'fields' => ['ttitle'],
                       ];
        $query = xarTableDDL::createIndex($topicstable, $index);
        $dbconn->Execute($query);

        // posts table
        $query = "DROP TABLE IF EXISTS " . $poststable;
        if (!$q->run($query)) {
            return;
        }
        $fields = [
            'id' => ['type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true],
            'tid' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'ptime' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'pstatus' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'powner' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'poststype' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'phostname' => ['type' => 'varchar','size' => 255,'null' => false, 'charset' => $charset],
            'pdesc' => ['type' => 'varchar','size' => 255,'null' => false, 'charset' => $charset],
            'ptext' => ['type' => 'text', 'charset' => $charset],
            'psettings' => ['type' => 'text', 'charset' => $charset],
        ];
        $query = xarTableDDL::createTable($poststable, $fields);
        $dbconn->Execute($query);

        // tid
        $index = ['name' => $prefix . '_crispbb_posts_tid',
                       'fields' => ['tid'],
                       ];
        $query = xarTableDDL::createIndex($poststable, $index);
        $dbconn->Execute($query);

        // ptime
        $index = ['name' => $prefix . '_crispbb_posts_ptime',
                       'fields' => ['ptime'],
                       ];
        $query = xarTableDDL::createIndex($poststable, $index);
        $dbconn->Execute($query);

        // pstatus
        $index = ['name' => $prefix . '_crispbb_posts_pstatus',
                       'fields' => ['pstatus'],
                       ];
        $query = xarTableDDL::createIndex($poststable, $index);
        $dbconn->Execute($query);

        // powner
        $index = ['name' => $prefix . '_crispbb_posts_powner',
                       'fields' => ['powner'],
                       ];
        $query = xarTableDDL::createIndex($poststable, $index);
        $dbconn->Execute($query);

        // hooks table
        $query = "DROP TABLE IF EXISTS " . $hookstable;
        if (!$q->run($query)) {
            return;
        }
        $fields = [
            'id' => ['type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true],
            'moduleid' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'itemtype' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'itemid' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'tid' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
        ];
        $query = xarTableDDL::createTable($hookstable, $fields);
        $dbconn->Execute($query);

        // moduleid
        $index = ['name' => $prefix . '_crispbb_hooks_moduleid',
                       'fields' => ['moduleid'],
                       ];
        $query = xarTableDDL::createIndex($hookstable, $index);
        $dbconn->Execute($query);

        // itemtype
        $index = ['name' => $prefix . '_crispbb_hooks_itemtype',
                       'fields' => ['itemtype'],
                       ];
        $query = xarTableDDL::createIndex($hookstable, $index);
        $dbconn->Execute($query);

        // itemid
        $index = ['name' => $prefix . '_crispbb_hooks_itemid',
                       'fields' => ['itemid'],
                       ];
        $query = xarTableDDL::createIndex($hookstable, $index);
        $dbconn->Execute($query);

        // tid
        $index = ['name' => $prefix . '_crispbb_hooks_tid',
                       'fields' => ['tid'],
                       ];
        $query = xarTableDDL::createIndex($hookstable, $index);
        $dbconn->Execute($query);

        // posters table
        $query = "DROP TABLE IF EXISTS " . $posterstable;
        if (!$q->run($query)) {
            return;
        }
        // @TODO: this could be waaaaay more useful (ranking, karma, etc)
        $fields = [
            'id' => ['type' => 'integer', 'unsigned' => true, 'null' => false, 'default' => '0'],
            'numtopics' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
            'numreplies' => ['type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'],
        ];
        $query = xarTableDDL::createTable($posterstable, $fields);
        $dbconn->Execute($query);
        $index = ['name' => $prefix . '_crispbb_posters_id',
                       'fields' => ['id'],
                       ];
        $query = xarTableDDL::createIndex($posterstable, $index);
        $dbconn->Execute($query);


        // We're done, commit
        $dbconn->commit();
    } catch (Exception $e) {
        $dbconn->rollback();
        throw $e;
    }

    # --------------------------------------------------------
#
    # Create DD objects
#
    $objects = [
                'crispbb_forums',
                'crispbb_topics',
                'crispbb_posts',
                'crispbb_itemtypes',
                //'crispbb_hooks',
                //'crispbb_user_settings',
                'crispbb_forum_settings',
                'crispbb_posters',
                ];

    if (!xarMod::apiFunc('modules', 'admin', 'standardinstall', ['module' => $module, 'objects' => $objects])) {
        return;
    }

    # --------------------------------------------------------
#
    # Create Base Itemtypes
#

    $itemtypes = DataObjectMaster::getObject(['name' => 'crispbb_itemtypes']);
    $components = ['forum', 'topics', 'posts'];
    foreach ($components as $component) {
        $itemtypes->properties['id']->value = 0;
        $basetypes[$component] = $itemtypes->createItem(['fid' => 0, 'component' => $component]);
    }
    # --------------------------------------------------------
#
    # Create Base (Root) Category
#
    $catName = 'crispBB Forums';
    try {
        sys::import('modules.categories.class.worker');
        $worker = new CategoryWorker();
        $basecid = $worker->name2id($catName);
    } catch (Exception $e) {
        $basecid = 0;
    }
    if (empty($basecid)) {
        sys::import('modules.dynamicdata.class.objects.master');
        $categories = DataObjectMaster::getObject(['name' => 'categories']);
        $fieldValues = [
                'name' => $catName,
                'description' => xarML('crispBB Root Category'),
                'parent_id' => 1,
        ];
        $basecid = $categories->createItem($fieldValues);
    }

    // Save the base category in a modvar
    xarModVars::set('crispbb', 'base_categories', serialize([$basecid]));

    # --------------------------------------------------------
#
    # Set up configuration modvars (module specific)
#

    // Module settings (storage for forums and module default settings)
    xarModVars::set($module, 'ftracking', serialize([]));
    xarModVars::set($module, 'forumsettings', serialize([]));
    xarModVars::set($module, 'privilegesettings', serialize([]));

    // The tracker class takes care of creating the tracker object
    sys::import('modules.crispbb.class.tracker');
    $tracker = new Tracker(true);
    unset($tracker); // unsetting here causes the modvar to be stored with default values :)

    $sessionTimeout = xarConfigVars::get(null, 'Site.Session.InactivityTimeout');
    // display options
    xarModVars::set($module, 'visit_timeout', $sessionTimeout);
    xarModVars::set($module, 'showuserpanel', true);
    xarModVars::set($module, 'showsearchbox', true);
    xarModVars::set($module, 'showforumjump', true);
    xarModVars::set($module, 'showtopicjump', true);
    xarModVars::set($module, 'showquickreply', true);
    xarModVars::set($module, 'showpermissions', true);
    xarModVars::set($module, 'showsortbox', true);
    xarModVars::set($module, 'editor', 'textarea');

    # --------------------------------------------------------
#
    # Set up configuration modvars (common)
#
    $module_settings = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => $module]);
    $module_settings->initialize();

    # --------------------------------------------------------
#
    # Register blocks
#
    // register topitems block
    if (!xarMod::apiFunc(
        'blocks',
        'admin',
        'register_block_type',
        ['modName' => $module,
                'blockType' => 'topitems', ]
    )) {
        return;
    }

    // register userpanel block
    if (!xarMod::apiFunc(
        'blocks',
        'admin',
        'register_block_type',
        ['modName' => $module,
                'blockType' => 'userpanel', ]
    )) {
        return;
    }
    # --------------------------------------------------------
#
    # Create privilege instances
#
    $instances = [
                       ['header' => 'external', // this keyword indicates an external "wizard"
                             'query'  => xarController::URL($module, 'admin', 'privileges'),
                             'limit'  => 0,
                            ],
                    ];
    xarPrivileges::defineInstance($module, 'Forum', $instances);

    $info = xarMod::getBaseInfo($module);
    $sysid = $info['systemid'];
    $query = "SELECT DISTINCT instances.title FROM $tables[block_instances] as instances LEFT JOIN $tables[block_types] as btypes ON btypes.id = instances.type_id WHERE module_id = $sysid";
    $instances = [
                        ['header' => 'crispBB Block Title:',
                                'query' => $query,
                                'limit' => 20,
                            ],
                    ];
    xarPrivileges::defineInstance($module, 'Block', $instances);

    # --------------------------------------------------------
#
    # Register masks
#
    xarMasks::register('ViewCrispBB', 'All', $module, 'Item', 'All:All', 'ACCESS_OVERVIEW');
    xarMasks::register('ReadCrispBB', 'All', $module, 'Item', 'All:All', 'ACCESS_READ');
    xarMasks::register('PostCrispBB', 'All', $module, 'Item', 'All:All', 'ACCESS_COMMENT');
    xarMasks::register('ModerateCrispBB', 'All', $module, 'Item', 'All:All', 'ACCESS_MODERATE');
    xarMasks::register('EditCrispBB', 'All', $module, 'Item', 'All:All', 'ACCESS_EDIT');
    xarMasks::register('AddCrispBB', 'All', $module, 'Item', 'All:All', 'ACCESS_ADD');
    xarMasks::register('DeleteCrispBB', 'All', $module, 'Item', 'All:All', 'ACCESS_DELETE');
    xarMasks::register('AdminCrispBB', 'All', $module, 'Item', 'All:All', 'ACCESS_ADMIN');

    xarMasks::register('ReadCrispBBBlock', 'All', $module, 'Block', 'All:All:All', 'ACCESS_READ');

    # --------------------------------------------------------
#
    # Register module hooks
#
    // register search hook
    if (!xarModHooks::register('item', 'search', 'GUI', $module, 'user', 'search')) {
        return false;
    }

    // register waiting content hook
    if (!xarModHooks::register('item', 'waitingcontent', 'GUI', $module, 'admin', 'waitingcontent')) {
        return false;
    }

    // Module Modify Config
    if (!xarModHooks::register(
        'module',
        'modifyconfig',
        'GUI',
        $module,
        'admin',
        'modifyconfighook'
    )) {
        return false;
    }

    // Module Update Config
    if (!xarModHooks::register(
        'module',
        'updateconfig',
        'API',
        $module,
        'admin',
        'updateconfighook'
    )) {
        return false;
    }

    // Module Remove
    if (!xarModHooks::register('module', 'remove', 'API', $module, 'admin', 'removehook')) {
        return false;
    }

    // Display item
    if (!xarModHooks::register('item', 'display', 'GUI', $module, 'user', 'displayhook')) {
        return false;
    }

    // Delete item
    if (!xarModHooks::register('item', 'delete', 'API', $module, 'user', 'deletehook')) {
        return false;
    }

    # --------------------------------------------------------
#
    # Register hooks from other modules
#

    // hook hitcount to all topics
    xarMod::apiFunc(
        'modules',
        'admin',
        'enablehooks',
        [
            'callerModName' => $module,
            'callerItemType' => $basetypes['topics'],
            'hookModName' => 'hitcount',
        ]
    );

    // enable waiting content hook for base module
    xarMod::apiFunc(
        'modules',
        'admin',
        'enablehooks',
        ['callerModName' => 'base', 'hookModName' => $module]
    );

    // hook search
    if (xarMod::isAvailable('search')) {
        xarMod::apiFunc(
            'modules',
            'admin',
            'enablehooks',
            ['callerModName' => 'search', 'hookModName' => $module]
        );
    }

    return crispbb_upgrade('2.0.0');
}

/**
 * Upgrade the module from an old version
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @param string oldversion. This function takes the old version currently stored in the module db
 * @return bool true on succes of upgrade
 * @throws mixed This function can throw all sorts of errors, depending on the functions present
                 Currently it can raise database errors.
 */
function crispbb_upgrade($oldversion)
{
    $module = 'crispbb';
    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $prefix = xarDB::getPrefix();
    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    $itemtypestable = $tables['crispbb_itemtypes'];
    $forumstable    = $tables['crispbb_forums'];
    $topicstable    = $tables['crispbb_topics'];
    $poststable     = $tables['crispbb_posts'];
    $hookstable     = $tables['crispbb_hooks'];
    $posterstable   = $tables['crispbb_posters'];

    switch ($oldversion) {
        // see xardocs/changelog.txt for full details of changes
        // module pushed to repo's
        case '2.0.0':
        // current version
        break;
    }
    // Update successful
    return true;
}

/**
 * Delete the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @author crisp <crisp@crispcreations.co.uk>
 * @param none
 * @return bool true on succes of deletion
 */
function crispbb_delete()
{
    $module = 'crispbb';
    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $prefix = xarDB::getPrefix();
    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    $itemtypestable = $tables['crispbb_itemtypes'];
    $forumstable    = $tables['crispbb_forums'];
    $topicstable    = $tables['crispbb_topics'];
    $poststable     = $tables['crispbb_posts'];
    $hookstable     = $tables['crispbb_hooks'];
    $posterstable   = $tables['crispbb_posters'];



    if (!xarModHooks::unregister(
        'item',
        'search',
        'GUI',
        $module,
        'user',
        'search'
    )) {
        return false;
    }

    if (!xarModHooks::unregister(
        'item',
        'waitingcontent',
        'GUI',
        $module,
        'admin',
        'waitingcontent'
    )) {
        return false;
    }

    if (!xarModHooks::unregister(
        'module',
        'modifyconfig',
        'GUI',
        $module,
        'admin',
        'modifyconfighook'
    )) {
        return false;
    }
    if (!xarModHooks::unregister(
        'module',
        'updateconfig',
        'API',
        $module,
        'admin',
        'updateconfighook'
    )) {
        return false;
    }
    if (!xarModHooks::unregister(
        'module',
        'remove',
        'API',
        $module,
        'admin',
        'removehook'
    )) {
        return false;
    }
    if (!xarModHooks::unregister(
        'item',
        'display',
        'GUI',
        $module,
        'user',
        'displayhook'
    )) {
        return false;
    }
    if (!xarModHooks::unregister(
        'item',
        'delete',
        'API',
        $module,
        'user',
        'deletehook'
    )) {
        return false;
    }
    if (!xarMod::apiFunc(
        'blocks',
        'admin',
        'unregister_block_type',
        ['modName' => $module,
                'blockType' => 'topitems', ]
    )) {
        return;
    }

    if (!xarMod::apiFunc(
        'blocks',
        'admin',
        'unregister_block_type',
        ['modName' => $module,
                'blockType' => 'userpanel', ]
    )) {
        return;
    }
    # --------------------------------------------------------
#
    # Uninstall the module
#
    # The function below pretty much takes care of everything else that needs to be removed
#
    // Remove the crispbb categories
    sys::import('modules.categories.class.worker');
    $worker = new CategoryWorker();
    $base_categories = unserialize(xarModVars::get('crispbb', 'base_categories'));
    foreach ($base_categories as $base_category) {
        $worker->delete($base_category);
    }

    // Remove the crispbb category links
    xarMod::apiFunc('categories', 'admin', 'unlinkcids', ['modid' => xarMod::getRegID('crispbb'), 'itemtype' => 1]);

    return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', ['module' => $module]);

    /* Deletion successful*/
    return true;
}
