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
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $forumstable = $xartable['crispbb_forums'];
    $itemtypestable = $xartable['crispbb_itemtypes'];
    $topicstable = $xartable['crispbb_topics'];
    $poststable = $xartable['crispbb_posts'];
    $hookstable = $xartable['crispbb_hooks'];

    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    /* forums table */
    $fields = "xar_fid          I           AUTO       PRIMARY,
               xar_fstatus      I4          NotNull    DEFAULT 0,
               xar_fowner       I           NotNull    DEFAULT 0,
               xar_forder       I           NotNull    DEFAULT 0,
               xar_lasttid      I           NotNull    DEFAULT 0,
               xar_ftype        I4          NotNull    DEFAULT 0,
               xar_fname        C(100)      NotNull   DEFAULT '',
               xar_fdesc        C(255)      NotNull   DEFAULT '',
               xar_fsettings    X          NotNull   DEFAULT '',
               xar_fprivileges  X          NotNull   DEFAULT ''
               ";
    $result = $datadict->changeTable($forumstable, $fields);
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_forum_fname',
        $forumstable,
        'xar_fname'
    );
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_forum_fstatus',
        $forumstable,
        'xar_fstatus'
    );
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_forum_forder',
        $forumstable,
        'xar_forder'
    );
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_forum_fowner',
        $forumstable,
        'xar_fowner'
    );
    if (!$result) {return;}

    /* itemtypes table */
    $fields = "xar_itemtype     I           AUTO       PRIMARY,
               xar_fid          I           NotNull    DEFAULT 0,
               xar_component    C(10)       NotNull   DEFAULT ''
              ";
    $result = $datadict->changeTable($itemtypestable, $fields);
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_itemtype_fid',
        $itemtypestable,
        'xar_fid'
    );
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_itemtype_component',
        $itemtypestable,
        'xar_component'
    );
    if (!$result) {return;}

    /* topics table */
    $fields = "xar_tid          I           AUTO       PRIMARY,
               xar_fid          I           NotNull    DEFAULT 0,
               xar_ttype        I           NotNull    DEFAULT 0,
               xar_tstatus      I4          NotNull    DEFAULT 0,
               xar_towner       I           NotNull    DEFAULT 0,
               xar_topicstype   I           NotNull    DEFAULT 0,
               xar_firstpid     I           NotNull    DEFAULT 0,
               xar_lastpid      I           NotNull    DEFAULT 0,
               xar_ttitle       C(255)      NotNull   DEFAULT '',
               xar_tsettings    X          NotNull   DEFAULT ''
               ";
    $result = $datadict->changeTable($topicstable, $fields);
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_topics_fid',
        $topicstable,
        'xar_fid'
    );
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_topics_ttype',
        $topicstable,
        'xar_ttype'
    );
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_topics_tstatus',
        $topicstable,
        'xar_tstatus'
    );
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_topics_towner',
        $topicstable,
        'xar_towner'
    );
    if (!$result) {return;}
     $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_topics_ttitle',
        $topicstable,
        'xar_ttitle'
    );
    if (!$result) {return;}

    /* posts table */
    $fields = "xar_pid          I           AUTO       PRIMARY,
               xar_tid          I           NotNull    DEFAULT 0,
               xar_ptime        I           NotNull    DEFAULT 0,
               xar_pstatus      I4          NotNull    DEFAULT 0,
               xar_powner       I           NotNull    DEFAULT 0,
               xar_poststype    I           NotNull    DEFAULT 0,
               xar_phostname    C(255)      NotNull   DEFAULT '',
               xar_pdesc        C(255)      NotNull   DEFAULT '',
               xar_psettings    X          NotNull   DEFAULT '',
               xar_ptext        X2          NotNull   DEFAULT ''
               ";
    $result = $datadict->changeTable($poststable, $fields);
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_posts_tid',
        $poststable,
        'xar_tid'
    );
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_posts_ptime',
        $poststable,
        'xar_ptime'
    );
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_posts_pstatus',
        $poststable,
        'xar_pstatus'
    );
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_posts_powner',
        $poststable,
        'xar_powner'
    );
    if (!$result) {return;}

    /* hooks table */
    $fields = "xar_hid          I           AUTO       PRIMARY,
               xar_moduleid     I           NotNull    DEFAULT 0,
               xar_itemtype     I           NotNull    DEFAULT 0,
               xar_itemid       I           NotNull    DEFAULT 0,
               xar_tid          I           NotNull    DEFAULT 0
              ";
    $result = $datadict->changeTable($hookstable, $fields);
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_hooks_moduleid',
        $hookstable,
        'xar_moduleid'
    );
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_hooks_itemtype',
        $hookstable,
        'xar_itemtype'
    );
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_hooks_itemid',
        $hookstable,
        'xar_itemid'
    );
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_crispbb_hooks_tid',
        $hookstable,
        'xar_tid'
    );
    if (!$result) {return;}

    $parentcat = xarModAPIFunc('categories', 'admin', 'create',
        array(
            'name' => 'crispBB Forums',
            'description' => xarML('Parent Category for crispBB Forums'),
            'parent_id' => 0
        ));
    xarModSetVar('crispbb', 'number_of_categories', 1);
    xarModSetVar('crispbb', 'mastercids', $parentcat);
    $categories = array();
    $categories[] = array('name' => 'Site Administration',
        'description' => xarML('Site Administration Forums'));
    $categories[] = array('name' => 'User Forums',
        'description' => xarML('User Forums'));
    $newcids = array();
    foreach ($categories as $subcat) {
        $newcids[] = xarModAPIFunc('categories', 'admin', 'create',
            array(
                'name' => $subcat['name'],
                'description' => $subcat['description'],
                'parent_id' => $parentcat
            ));
    }

    // create default itemtypes
    $forumtype = xarModAPIFunc('crispbb', 'admin', 'createitemtype',
        array('fid' => 0, 'component' => 'forum'));
    $topicstype = xarModAPIFunc('crispbb', 'admin', 'createitemtype',
        array('fid' => 0, 'component' => 'topics'));
    $poststype = xarModAPIFunc('crispbb', 'admin', 'createitemtype',
        array('fid' => 0, 'component' => 'posts'));

    // store parent category for crispbb
    xarModSetVar('crispbb', 'number_of_categories.'.$forumtype, 1);
    xarModSetVar('crispbb', 'mastercids.'.$forumtype, $parentcat);

    // hook categories to all forums
    xarModAPIFunc('modules','admin','enablehooks',
        array(
            'callerModName' => 'crispbb',
            'callerItemType' => $forumtype,
            'hookModName' => 'categories'
        ));

    // hook hitcount to all topics
    xarModAPIFunc('modules','admin','enablehooks',
        array(
            'callerModName' => 'crispbb',
            'callerItemType' => $topicstype,
            'hookModName' => 'hitcount'
        ));

    // set module vars
    xarModSetVar('crispbb', 'SupportShortURLs', false);
    xarModSetVar('crispbb', 'useModuleAlias', false);
    xarModSetVar('crispbb', 'aliasname', '');
    xarModSetVar('crispbb', 'tracking', serialize(array()));
    xarModSetVar('crispbb', 'ftracking', serialize(array()));
    xarModSetVar('crispbb', 'forumsettings', serialize(array()));
    xarModSetVar('crispbb', 'privilegesettings', serialize(array()));

    // define privilege instances
    $instances = array(
        array(
            'header' => 'external',
            'query'  => xarModURL('crispbb','admin','privileges'),
            'limit'  => 0
      ));
    xarDefineInstance('crispbb', 'Forum', $instances);
    // register priv masks
    xarRegisterMask('ViewCrispBB',   'All', 'crispbb', 'Forum', 'All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadCrispBB',   'All', 'crispbb', 'Forum', 'All:All', 'ACCESS_READ');
    xarRegisterMask('PostCrispBB',   'All', 'crispbb', 'Forum', 'All:All', 'ACCESS_COMMENT');
    xarRegisterMask('ModerateCrispBB',   'All', 'crispbb', 'Forum', 'All:All', 'ACCESS_MODERATE');
    xarRegisterMask('EditCrispBB',   'All', 'crispbb', 'Forum', 'All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddCrispBB',    'All', 'crispbb', 'Forum', 'All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteCrispBB', 'All', 'crispbb', 'Forum', 'All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminCrispBB',  'All', 'crispbb', 'Forum', 'All:All', 'ACCESS_ADMIN');

    // register search hook
    if (!xarModRegisterHook('item', 'search', 'GUI', 'crispbb', 'user', 'search')) {
       return false;
    }
    if (xarModIsAvailable('search')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'search', 'hookModName' => 'crispbb'));
    }

    // register topitems block, block instance, and mask
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'crispbb',
                'blockType' => 'topitems'))) return;
    $instancestable = $xartable['block_instances'];
    $typestable = $xartable['block_types'];
    $query = "SELECT DISTINCT i.xar_id, i.xar_name FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'crispbb'";
    $instances = array(
        array('header' => 'CrispBB Block ID:',
            'query' => $query,
            'limit' => 20
            )
        );
    xarDefineInstance('crispbb', 'Block', $instances);
    xarRegisterMask('ReadCrispBBBlock', 'All', 'crispbb', 'Block', 'All', 'ACCESS_OVERVIEW');

    // Added BL tag to show topic replies
    xarTplRegisterTag('crispbb', 'crispbb-showreplies',
                      array(),
                      'crispbb_userapi_showrepliestag');

    return crispbb_upgrade('0.5.0');
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
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $forumstable = $xartable['crispbb_forums'];
    $itemtypestable = $xartable['crispbb_itemtypes'];
    $topicstable = $xartable['crispbb_topics'];
    $poststable = $xartable['crispbb_posts'];
    $hookstable = $xartable['crispbb_hooks'];

    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    switch ($oldversion) {
        // see xardocs/changelog.txt for details
        // module pushed to repo's
        case '0.5.0':
            // bugfix links to purge topics
        case '0.5.1':
            // add some checks on unserialize funcs in getforums api func
        case '0.5.2':
            // bugfixes, template clean-up and add check for updates function
        case '0.5.6':
            // more template clean up, move timeimages folder
        case '0.5.7':
            // add topicicons-crispbb and make default for new installs
            // admin configurable display settings
            xarModSetVar('crispbb', 'showuserpanel', 1);
            xarModSetVar('crispbb', 'showsearchbox', 1);
            xarModSetVar('crispbb', 'showforumjump', 1);
            xarModSetVar('crispbb', 'showtopicjump', 1);
            xarModSetVar('crispbb', 'showquickreply', 1);
            xarModSetVar('crispbb', 'showpermissions', 1);
        case '0.5.9':
            // add topic-reply-row.xd include
            // register userpanel block
            if (!xarModAPIFunc('blocks',
                    'admin',
                    'register_block_type',
                    array('modName' => 'crispbb',
                        'blockType' => 'userpanel'))) return;
        case '0.6.0':
            // admin template clean up
            // added redirect forum type
        case '0.6.1':
            /* current version */

        break;
    }
    /* Update successful */
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
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $forumstable = $xartable['crispbb_forums'];
    $itemtypestable = $xartable['crispbb_itemtypes'];
    $topicstable = $xartable['crispbb_topics'];
    $poststable = $xartable['crispbb_posts'];
    $hookstable = $xartable['crispbb_hooks'];

    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $result = $datadict->dropTable($itemtypestable);
    $result = $datadict->dropTable($forumstable);
    $result = $datadict->dropTable($topicstable);
    $result = $datadict->dropTable($poststable);
    $result = $datadict->dropTable($hookstable);


    $aliasname = xarModGetVar('crispbb','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='crispbb')) {
        xarModDelAlias($aliasname,'crispbb');
    }

    xarModDelAllVars('crispbb');

    if (!xarModUnregisterHook('item', 'search', 'GUI',
                              'crispbb', 'user', 'search')) {
        return false;
    }

    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'crispbb',
                'blockType' => 'topitems'))) return;

    xarRemoveMasks('crispbb');
    xarRemoveInstances('crispbb');
    xarTplUnregisterTag('crispbb-showreplies');
    /* Deletion successful*/
    return true;
}
?>
