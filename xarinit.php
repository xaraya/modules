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
    $dbconn =& xarDB::getConn();
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

        // forums table
        $fields = array(
            'id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
            'fstatus' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'fowner' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'forder' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'lasttid' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'ftype' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'numtopics' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'numreplies' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'numtopicsubs' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'numreplysubs' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'numtopicdels' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'numreplydels' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'fname' => array('type' => 'varchar','size' => 100,'null' => false, 'charset' => $charset),
            'fdesc' => array('type' => 'varchar','size' => 255,'null' => false, 'charset' => $charset),
            'fsettings' => array('type' => 'text', 'charset' => $charset),
            'fprivileges' => array('type' => 'text', 'charset' => $charset)
        );
        $query = xarDBCreateTable($forumstable,$fields);
        $dbconn->Execute($query);

        // fstatus
        $index = array('name' => $prefix . '_crispbb_forums_fstatus',
                       'fields' => array('fstatus')
                       );
        $query = xarDBCreateIndex($forumstable, $index);
        $dbconn->Execute($query);

        // forder
        $index = array('name' => $prefix . '_crispbb_forums_forder',
                       'fields' => array('forder')
                       );
        $query = xarDBCreateIndex($forumstable, $index);
        $dbconn->Execute($query);

        // fname
        $index = array('name' => $prefix . '_crispbb_forums_fname',
                       'fields' => array('fname')
                       );
        $query = xarDBCreateIndex($forumstable, $index);
        $dbconn->Execute($query);

        // itemtypes table
        $fields = array(
            'id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
            'fid' => array('type' => 'integer', 'unsigned' => true, 'null' => false),
            'component' => array('type' => 'varchar','size' => 10,'null' => false, 'charset' => $charset),
        );
        $query = xarDBCreateTable($itemtypestable,$fields);
        $dbconn->Execute($query);

        // itemtypes, every entry must be unique
        $index = array('name' => $prefix . '_crispbb_itemtypes_combo',
                       'fields' => array('fid', 'component'),
                       'unique' => true,
                       );
        $query = xarDBCreateIndex($itemtypestable, $index);
        $dbconn->Execute($query);

        // topics table
        $fields = array(
            'id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
            'fid' => array('type' => 'integer', 'unsigned' => true, 'null' => false),
            'ttype' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'tstatus' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'towner' => array('type' => 'integer', 'unsigned' => true, 'null' => false),
            'topicstype' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'firstpid' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'lastpid' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'numreplies' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'numsubs' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'numdels' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'ttitle' => array('type' => 'varchar','size' => 255,'null' => false, 'charset' => $charset),
            'tsettings' => array('type' => 'text', 'charset' => $charset),
        );
        $query = xarDBCreateTable($topicstable,$fields);
        $dbconn->Execute($query);

        // fid
        $index = array('name' => $prefix . '_crispbb_topics_fid',
                       'fields' => array('fid')
                       );
        $query = xarDBCreateIndex($topicstable, $index);
        $dbconn->Execute($query);

        // ttype
        $index = array('name' => $prefix . '_crispbb_topics_ttype',
                       'fields' => array('ttype')
                       );
        $query = xarDBCreateIndex($topicstable, $index);
        $dbconn->Execute($query);

        // tstatus
        $index = array('name' => $prefix . '_crispbb_topics_tstatus',
                       'fields' => array('tstatus')
                       );
        $query = xarDBCreateIndex($topicstable, $index);
        $dbconn->Execute($query);

        // towner
        /*
        $index = array('name' => $prefix . '_crispbb_topics_towner',
                       'fields' => array('towner')
                       );
        $query = xarDBCreateIndex($topicstable, $index);
        $dbconn->Execute($query);
        */
        // ttitle
        $index = array('name' => $prefix . '_crispbb_topics_ttitle',
                       'fields' => array('ttitle')
                       );
        $query = xarDBCreateIndex($topicstable, $index);
        $dbconn->Execute($query);

        // posts table
        $fields = array(
            'id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
            'tid' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'ptime' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'pstatus' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'powner' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'poststype' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'phostname' => array('type' => 'varchar','size' => 255,'null' => false, 'charset' => $charset),
            'pdesc' => array('type' => 'varchar','size' => 255,'null' => false, 'charset' => $charset),
            'ptext' => array('type' => 'text', 'charset' => $charset),
            'psettings' => array('type' => 'text', 'charset' => $charset),
        );
        $query = xarDBCreateTable($poststable,$fields);
        $dbconn->Execute($query);

        // tid
        $index = array('name' => $prefix . '_crispbb_posts_tid',
                       'fields' => array('tid')
                       );
        $query = xarDBCreateIndex($poststable, $index);
        $dbconn->Execute($query);

        // ptime
        $index = array('name' => $prefix . '_crispbb_posts_ptime',
                       'fields' => array('ptime')
                       );
        $query = xarDBCreateIndex($poststable, $index);
        $dbconn->Execute($query);

        // pstatus
        $index = array('name' => $prefix . '_crispbb_posts_pstatus',
                       'fields' => array('pstatus')
                       );
        $query = xarDBCreateIndex($poststable, $index);
        $dbconn->Execute($query);

        // powner
        $index = array('name' => $prefix . '_crispbb_posts_powner',
                       'fields' => array('powner')
                       );
        $query = xarDBCreateIndex($poststable, $index);
        $dbconn->Execute($query);

        // hooks table
        $fields = array(
            'id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
            'moduleid' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'itemtype' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'itemid' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'tid' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
        );
        $query = xarDBCreateTable($hookstable,$fields);
        $dbconn->Execute($query);

        // moduleid
        $index = array('name' => $prefix . '_crispbb_hooks_moduleid',
                       'fields' => array('moduleid')
                       );
        $query = xarDBCreateIndex($hookstable, $index);
        $dbconn->Execute($query);

        // itemtype
        $index = array('name' => $prefix . '_crispbb_hooks_itemtype',
                       'fields' => array('itemtype')
                       );
        $query = xarDBCreateIndex($hookstable, $index);
        $dbconn->Execute($query);

        // itemid
        $index = array('name' => $prefix . '_crispbb_hooks_itemid',
                       'fields' => array('itemid')
                       );
        $query = xarDBCreateIndex($hookstable, $index);
        $dbconn->Execute($query);

        // tid
        $index = array('name' => $prefix . '_crispbb_hooks_tid',
                       'fields' => array('tid')
                       );
        $query = xarDBCreateIndex($hookstable, $index);
        $dbconn->Execute($query);

        // posters table
        // @TODO: this could be waaaaay more useful (ranking, karma, etc)
        $fields = array(
            'id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'default' => '0'),
            'numtopics' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'numreplies' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
        );
        $query = xarDBCreateTable($posterstable,$fields);
        $dbconn->Execute($query);
        $index = array('name' => $prefix . '_crispbb_posters_id',
                       'fields' => array('id')
                       );
        $query = xarDBCreateIndex($posterstable, $index);
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
    $objects = array(
                'crispbb_forums',
                //'crispbb_topics',
                //'crispbb_posts',
                'crispbb_itemtypes',
                //'crispbb_hooks',
                //'crispbb_user_settings',
                'crispbb_forum_settings',
                //'crispbb_posters',
                );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

# --------------------------------------------------------
#
# Create Base Itemtypes
#

    $itemtypes = DataObjectMaster::getObject(array('name' => 'crispbb_itemtypes'));
    $components = array('forum', 'topics', 'posts');
    foreach ($components as $component => $label)
        $basetypes[$component] = $itemtypes->createItem(array('fid' => 0, 'component' => $component));

# --------------------------------------------------------
#
# Create Base Category
#
    $basecid = xarMod::apiFunc('categories', 'admin', 'create',
        array(
            'name' => 'crispBB Forums',
            'description' => 'crispBB Base Category',
            'parent_id' => 0,
        ));

    if (!xarMod::apiFunc('categories', 'admin', 'setcatbases',
        array('module' => $module, 'cids' => array($basecid)))) return;


# --------------------------------------------------------
#
# Set up configuration modvars (module specific)
#

    // module settings (storage for forums and module default settings)
    xarModVars::set($module, 'ftracking', serialize(array()));
    xarModVars::set($module, 'forumsettings', serialize(array()));
    xarModVars::set($module, 'privilegesettings', serialize(array()));

    // the tracker class takes care of creating the tracker object
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

# --------------------------------------------------------
#
# Set up configuration modvars (common)
#
    $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => $module));
    $module_settings->initialize();

# --------------------------------------------------------
#
# Register blocks
#
    // register topitems block
    if (!xarMod::apiFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => $module,
                'blockType' => 'topitems'))) return;

    // register userpanel block
    if (!xarMod::apiFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => $module,
                'blockType' => 'userpanel'))) return;
# --------------------------------------------------------
#
# Create privilege instances
#
    $instances = array(
                       array('header' => 'external', // this keyword indicates an external "wizard"
                             'query'  => xarModURL($module, 'admin', 'privileges'),
                             'limit'  => 0
                            )
                    );
    xarDefineInstance($module, 'Forum', $instances);

    $info = xarMod::getBaseInfo($module);
    $sysid = $info['systemid'];
    $query = "SELECT DISTINCT instances.title FROM $tables[block_instances] as instances LEFT JOIN $tables[block_types] as btypes ON btypes.id = instances.type_id WHERE module_id = $sysid";
    $instances = array(
                        array('header' => 'crispBB Block Title:',
                                'query' => $query,
                                'limit' => 20
                            )
                    );
    xarDefineInstance($module,'Block',$instances);

# --------------------------------------------------------
#
# Register masks
#
    xarRegisterMask('ViewCrispBB','All',$module,'Item','All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadCrispBB','All',$module,'Item','All:All','ACCESS_READ');
    xarRegisterMask('PostCrispBB','All',$module,'Item','All:All','ACCESS_COMMENT');
    xarRegisterMask('ModerateCrispBB','All',$module,'Item','All:All','ACCESS_MODERATE');
    xarRegisterMask('EditCrispBB','All',$module,'Item','All:All','ACCESS_EDIT');
    xarRegisterMask('AddCrispBB','All',$module,'Item','All:All','ACCESS_ADD');
    xarRegisterMask('DeleteCrispBB','All',$module,'Item','All:All','ACCESS_DELETE');
    xarRegisterMask('AdminCrispBB','All',$module,'Item','All:All','ACCESS_ADMIN');

    xarRegisterMask('ReadCrispBBBlock','All',$module,'Block','All:All:All','ACCESS_READ');

# --------------------------------------------------------
#
# Register module hooks
#
    // register search hook
    if (!xarModRegisterHook('item', 'search', 'GUI', $module, 'user', 'search')) {
       return false;
    }

    // register waiting content hook
    if (!xarModRegisterHook('item', 'waitingcontent', 'GUI', $module, 'admin', 'waitingcontent')) {
       return false;
    }

    // Module Modify Config
    if (!xarModRegisterHook('module', 'modifyconfig', 'GUI', $module, 'admin',
        'modifyconfighook')) return false;

    // Module Update Config
    if (!xarModRegisterHook('module', 'updateconfig', 'API', $module, 'admin',
        'updateconfighook')) return false;

    // Module Remove
    if (!xarModRegisterHook('module', 'remove', 'API', $module, 'admin', 'removehook'))
        return false;

    // Display item
    if (!xarModRegisterHook('item', 'display', 'GUI', $module, 'user', 'displayhook'))
        return false;

    // Delete item
    if (!xarModRegisterHook('item', 'delete', 'API', $module, 'user', 'deletehook'))
        return false;

# --------------------------------------------------------
#
# Register hooks from other modules
#

    // hook hitcount to all topics
    xarMod::apiFunc('modules','admin','enablehooks',
        array(
            'callerModName' => $module,
            'callerItemType' => $basetypes['topics'],
            'hookModName' => 'hitcount'
        ));

    // enable waiting content hook for base module
    xarMod::apiFunc('modules','admin','enablehooks',
                  array('callerModName' => 'base', 'hookModName' => $module));

    // hook search
    if (xarModIsAvailable('search')) {
        xarMod::apiFunc('modules','admin','enablehooks',
                      array('callerModName' => 'search', 'hookModName' => $module));
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
    $dbconn =& xarDB::getConn();
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
    $dbconn =& xarDB::getConn();
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



    if (!xarModUnregisterHook('item', 'search', 'GUI',
                              $module, 'user', 'search')) {
        return false;
    }

    if (!xarModUnregisterHook('item', 'waitingcontent', 'GUI',
                              $module, 'admin', 'waitingcontent')) {
        return false;
    }

    if (!xarModUnregisterHook('module', 'modifyconfig', 'GUI',
                              $module, 'admin', 'modifyconfighook')) {
        return false;
    }
    if (!xarModUnregisterHook('module', 'updateconfig', 'API',
                              $module, 'admin', 'updateconfighook')) {
        return false;
    }
    if (!xarModUnregisterHook('module', 'remove', 'API',
                              $module, 'admin', 'removehook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'display', 'GUI',
                              $module, 'user', 'displayhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'delete', 'API',
                              $module, 'user', 'deletehook')) {
        return false;
    }
    if (!xarMod::apiFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => $module,
                'blockType' => 'topitems'))) return;

    if (!xarMod::apiFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => $module,
                'blockType' => 'userpanel'))) return;
# --------------------------------------------------------
#
# Uninstall the module
#
# The function below pretty much takes care of everything else that needs to be removed
#
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $module));

    /* Deletion successful*/
    return true;
}
?>