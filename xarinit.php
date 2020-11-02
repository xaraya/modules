<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Install this module
 */

function publications_init()
{
    sys::import('xaraya.structures.query');
    $xartable =& xarDB::getTables();

    # --------------------------------------------------------
#
    # Set tables
#
    $q = new Query();
    $prefix = xarDB::getPrefix();

    $query = "DROP TABLE IF EXISTS " . $prefix . "_publications";
    if (!$q->run($query)) {
        return;
    }
    $query = "CREATE TABLE " . $prefix . "_publications (
            id                  integer unsigned NOT NULL auto_increment,
            name                varchar(64) NOT NULL DEFAULT '',
            title               varchar(255) NOT NULL DEFAULT '',
            description         text,
            summary             text,
            body1               text,
            body2               text,
            body3               text,
            body4               text,
            body5               text,
            notes               text,
            seq                 integer unsigned NOT NULL DEFAULT '0',
            parent_id           integer unsigned NOT NULL DEFAULT '0',
            pubtype_id          tinyint NOT NULL DEFAULT '1',
            pagetype_id         tinyint NOT NULL DEFAULT '1',
            pages               integer unsigned NOT NULL DEFAULT '1',
            locale              varchar(64) NOT NULL DEFAULT '',
            page_title          varchar(255) NOT NULL DEFAULT '',
            page_description    text,
            keywords            text,
            leftpage_id         integer unsigned NULL,
            rightpage_id        integer unsigned NULL,
            parentpage_id       integer unsigned NULL,
            start_date          integer unsigned NOT NULL,
            end_date            integer unsigned NOT NULL,
            no_end              tinyint NOT NULL DEFAULT '1',
            owner               integer unsigned NULL,
            version             integer unsigned NULL,
            create_date         integer unsigned NULL,
            modify_date         integer unsigned NULL,
            summary_template    tinyint NOT NULL DEFAULT '0',
            detail_template     tinyint NOT NULL DEFAULT '0',
            page_template       varchar(255) NOT NULL DEFAULT '',
            theme               varchar(64) NOT NULL DEFAULT '',
            sitemap_flag        tinyint NOT NULL DEFAULT '0',
            sitemap_source_flag tinyint NOT NULL DEFAULT '0',
            sitemap_alias       varchar(64) NOT NULL DEFAULT '',
            menu_flag           tinyint NOT NULL DEFAULT '0',
            menu_source_flag    tinyint NOT NULL DEFAULT '0',
            menu_alias          varchar(64) NOT NULL DEFAULT '',
            access              text,
            state               tinyint NOT NULL DEFAULT '3',
            process_state       tinyint NOT NULL DEFAULT '1',
            redirect_flag       tinyint NOT NULL DEFAULT '0',
            redirect_url        varchar(255) NOT NULL DEFAULT '',
            proxy_url           varchar(255) NOT NULL DEFAULT '',
            alias_flag          tinyint NOT NULL DEFAULT '0',
            alias               varchar(64) NOT NULL DEFAULT '',
            PRIMARY KEY(id),
            KEY owner (owner),
            KEY pubtype_id (pubtype_id),
            KEY state (state)
            )";
    if (!$q->run($query)) {
        return;
    }

    $query = "DROP TABLE IF EXISTS " . $prefix . "_publications_types";
    if (!$q->run($query)) {
        return;
    }
    $query = "CREATE TABLE " . $prefix . "_publications_types (
            id                  integer unsigned NOT NULL auto_increment,
            name                varchar(64) NOT NULL DEFAULT '',
            description         varchar(255) NOT NULL DEFAULT '',
            template            varchar(255) NOT NULL DEFAULT '',
            page_title          varchar(255) NOT NULL DEFAULT '',
            page_description    text,
            keywords            text,
            summary_template    tinyint NOT NULL DEFAULT '0',
            detail_template     tinyint NOT NULL DEFAULT '0',
            page_template       varchar(255) NOT NULL DEFAULT '',
            theme               varchar(64) NOT NULL DEFAULT '',
            sitemap_source_flag tinyint NOT NULL DEFAULT '0',
            menu_source_flag    tinyint NOT NULL DEFAULT '0',
            configuration       text,
            access              text,
            state               tinyint unsigned NOT NULL DEFAULT '0',
            PRIMARY KEY(id))";
    if (!$q->run($query)) {
        return;
    }

    $query = "DROP TABLE IF EXISTS " . $prefix . "_publications_versions";
    if (!$q->run($query)) {
        return;
    }
    $query = "CREATE TABLE " . $prefix . "_publications_versions (
      id                integer unsigned NOT NULL auto_increment,
      page_id           integer unsigned default 0,
      owner             integer unsigned default 0,
      version_number    integer default NULL,
      version_date      integer default NULL,
      operation         varchar(254) default '',
      content           text,
      PRIMARY KEY  (id)
    )";
    if (!$q->run($query)) {
        return;
    }

    # --------------------------------------------------------
#
    # Create DD objects
#
    $module = 'publications';
    $objects = array(
                     'publications_types',
                     'publications_documents',
                     'publications_downloads',
                     'publications_faqs',
                     'publications_generic',
                     'publications_news',
                     'publications_pictures',
                     'publications_quotes',
                     'publications_reviews',
                     'publications_translations',
                     'publications_weblinks',
                     'publications_publications',
                     'publications_blog',
                     'publications_catalogue',
                     'publications_versions',
                     );

    if (!xarMod::apiFunc('modules', 'admin', 'standardinstall', array('module' => $module, 'objects' => $objects))) {
        return;
    }

    $categories = array();
    // Create publications categories
    $cids = array();
    foreach ($categories as $category) {
        $cid[$category['name']] = xarMod::apiFunc(
            'categories',
            'admin',
            'create',
            array('name' => $category['name'],
                              'description' => $category['description'],
                              'parent_id' => 0)
        );
        foreach ($category['children'] as $child) {
            $cid[$child] = xarMod::apiFunc(
                'categories',
                'admin',
                'create',
                array('name' => $child,
                              'description' => $child,
                              'parent_id' => $cid[$category['name']])
            );
        }
    }

    # --------------------------------------------------------
#
    # Set up modvars
#
    xarModVars::set('publications', 'items_per_page', 20);
    xarModVars::set('publications', 'use_module_alias', 0);
    xarModVars::set('publications', 'module_alias_name', 'Publications');
    xarModVars::set('publications', 'defaultmastertable', 'publications_documents');
    xarModVars::set('publications', 'use_module_icons', 1);
    xarModVars::set('publications', 'fulltextsearch', '');
    xarModVars::set('publications', 'defaultpubtype', 2);
    xarModVars::set('publications', 'defaultlanguage', 'en_US.utf-8');
    xarModVars::set('publications', 'defaultpage', 1);
    xarModVars::set('publications', 'errorpage', 2);
    xarModVars::set('publications', 'notfoundpage', 3);
    xarModVars::set('publications', 'noprivspage', 4);
    xarModVars::set('publications', 'debugmode', false);
    xarModVars::get('publications', 'multilanguage', true);
    xarModVars::set('publications', 'frontend_page', '[publications:user:display]&id=1');
    xarModVars::set('publications', 'backend_page', '[publications:admin:view_pages]');
    xarModVars::set('publications', 'use_process_states', 0);
    xarModVars::set('publications', 'use_versions', 0);
    xarModVars::set('publications', 'hide_tree_display', 0);
    xarModVars::set('publications', 'admin_override', 0);

    // Save publications settings for each publication type
    /*
    foreach ($settings as $id => $values) {
        if (isset($pubid[$id])) {
            $id = $pubid[$id];
        }
        // replace category names with cids
        if (isset($values['categories'])) {
            $cidlist = array();
            foreach ($values['categories'] as $catname) {
                if (isset($cid[$catname])) {
                    $cidlist[] = $cid[$catname];
                }
            }
            unset($values['categories']);
            if (!empty($id)) {
                xarModVars::set('publications', 'number_of_categories.'.$id, count($cidlist));
                xarModVars::set('publications', 'mastercids.'.$id, join(';',$cidlist));
            } else {
                xarModVars::set('publications', 'number_of_categories', count($cidlist));
                xarModVars::set('publications', 'mastercids', join(';',$cidlist));
            }
        } elseif (!empty($id)) {
            xarModVars::set('publications', 'number_of_categories.'.$id, 0);
            xarModVars::set('publications', 'mastercids.'.$id, '');
        } else {
            xarModVars::set('publications', 'number_of_categories', 0);
            xarModVars::set('publications', 'mastercids', '');
        }
        if (isset($values['defaultview']) && !is_numeric($values['defaultview'])) {
            if (isset($cid[$values['defaultview']])) {
                $values['defaultview'] = 'c' . $cid[$values['defaultview']];
            } else {
                $values['defaultview'] = 1;
            }
        }
        if (!empty($id)) {
            xarModVars::set('publications', 'settings.'.$id,serialize($values));
        } else {
            xarModVars::set('publications', 'settings',serialize($values));
        }
    }

*/

    /*

        if (!xarModHooks::register('item', 'search', 'GUI',
                               'publications', 'user', 'search')) {
            return false;
        }

        if (!xarModHooks::register('item', 'waitingcontent', 'GUI',
                               'publications', 'admin', 'waitingcontent')) {
            return false;
        }
        */

    # --------------------------------------------------------
#
    # Set up hooks
#
    xarHooks::registerSubject('ItemCreate', 'item', 'publications');
    xarHooks::registerSubject('ItemUpdate', 'item', 'publications');
    xarHooks::registerSubject('ItemDelete', 'item', 'publications');

    sys::import('xaraya.structures.hooks.observer');

    // Enable publications hooks for search
    if (xarMod::isAvailable('search')) {
        xarMod::apiFunc(
            'modules',
            'admin',
            'enablehooks',
            array('callerModName' => 'search', 'hookModName' => 'publications')
        );
    }

    // Enable categories hooks for publications
    /*    xarMod::apiFunc('modules','admin','enablehooks',
                      array('callerModName' => 'publications', 'hookModName' => 'categories'));
    */
    // Enable comments hooks for publications
    if (xarMod::isAvailable('comments')) {
        xarMod::apiFunc(
            'modules',
            'admin',
            'enablehooks',
            array('callerModName' => 'publications', 'hookModName' => 'comments')
        );
    }
    // Enable hitcount hooks for publications
    if (xarMod::isAvailable('hitcount')) {
        xarMod::apiFunc(
            'modules',
            'admin',
            'enablehooks',
            array('callerModName' => 'publications', 'hookModName' => 'hitcount')
        );
    }
    // Enable ratings hooks for publications
    if (xarMod::isAvailable('ratings')) {
        xarMod::apiFunc(
            'modules',
            'admin',
            'enablehooks',
            array('callerModName' => 'publications', 'hookModName' => 'ratings')
        );
    }

    /*********************************************************************
    * Define instances for the core modules
    * Format is
    * xarDefineInstance(Module,Component,Querystring,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/
    $info = xarMod::getBaseInfo('publications');
    $sysid = $info['systemid'];
    $xartable =& xarDB::getTables();
    $instances = array(
                       array('header' => 'external', // this keyword indicates an external "wizard"
                             'query'  => xarController::URL('publications', 'admin', 'privileges'),
                             'limit'  => 0
                            )
                    );
    xarDefineInstance('publications', 'Publication', $instances);

    $query = "SELECT DISTINCT instances.title FROM $xartable[block_instances] as instances LEFT JOIN $xartable[block_types] as btypes ON btypes.id = instances.type_id WHERE modid = $sysid";
    $instances = array(
                        array('header' => 'Publication Block Title:',
                                'query' => $query,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('publications', 'Block', $instances);

    # --------------------------------------------------------
#
    # Set up masks
#
    xarRegisterMask('ViewPublications', 'All', 'publications', 'All', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadPublications', 'All', 'publications', 'All', 'All', 'ACCESS_READ');
    xarRegisterMask('SubmitPublications', 'All', 'publications', 'All', 'All', 'ACCESS_COMMENT');
    xarRegisterMask('ModeratePublications', 'All', 'publications', 'All', 'All', 'ACCESS_MODERATE');
    xarRegisterMask('EditPublications', 'All', 'publications', 'All', 'All', 'ACCESS_EDIT');
    xarRegisterMask('AddPublications', 'All', 'publications', 'All', 'All', 'ACCESS_ADD');
    xarRegisterMask('ManagePublications', 'All', 'publications', 'All', 'All', 'ACCESS_DELETE');
    xarRegisterMask('AdminPublications', 'All', 'publications', 'All', 'All', 'ACCESS_ADMIN');

    # --------------------------------------------------------
#
    # Set up privileges
#
    xarRegisterPrivilege('ViewPublications', 'All', 'publications', 'All', 'All', 'ACCESS_OVERVIEW');
    xarRegisterPrivilege('ReadPublications', 'All', 'publications', 'All', 'All', 'ACCESS_READ');
    xarRegisterPrivilege('SubmitPublications', 'All', 'publications', 'All', 'All', 'ACCESS_COMMENT');
    xarRegisterPrivilege('ModeratePublications', 'All', 'publications', 'All', 'All', 'ACCESS_MODERATE');
    xarRegisterPrivilege('EditPublications', 'All', 'publications', 'All', 'All', 'ACCESS_EDIT');
    xarRegisterPrivilege('AddPublications', 'All', 'publications', 'All', 'All', 'ACCESS_ADD');
    xarRegisterPrivilege('ManagePublications', 'All', 'publications', 'All', 'All', 'ACCESS_DELETE');
    xarRegisterPrivilege('AdminPublications', 'All', 'publications', 'All', 'All', 'ACCESS_ADMIN');

    xarRegisterMask('ReadPublicationsBlock', 'All', 'publications', 'Block', 'All', 'ACCESS_READ');

    // Initialisation successful
    return true;
}

/**
 * Upgrade this module from an old version
 */
 
function publications_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {

        case '2.0.0':
            // Code to upgrade from version 2.0 goes here

        case '2.1.0':
            // Code to upgrade from version 2.1 goes here
            break;
    }
    return true;
}

/**
 * Remove this module
 */
 
function publications_delete()
{
    $module = 'publications';
    return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', array('module' => $module));
}
