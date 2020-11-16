<?php
/**
 * Release initialization functions
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
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
    sys::import('xaraya.structures.query');
    $xartable =& xarDB::getTables();

    # --------------------------------------------------------
#
    # Set tables
#
    $q = new Query();
    $prefix = xarDB::getPrefix();

    $query = "DROP TABLE IF EXISTS " . $prefix . "_release_extensions";
    if (!$q->run($query)) {
        return;
    }
    $query = "CREATE TABLE " . $prefix . "_release_extensions (
            id                  integer unsigned NOT NULL auto_increment,
            extension_id        integer unsigned NOT NULL DEFAULT '0',
            author_id           integer unsigned NOT NULL DEFAULT '0',
            name                varchar(64) NOT NULL DEFAULT '',
            display_name        varchar(64) NOT NULL DEFAULT '',
            description         text,
            class               tinyint NOT NULL DEFAULT '1',
            certified           tinyint NOT NULL DEFAULT '0',
            approved            tinyint NOT NULL DEFAULT '0',
            state               tinyint NOT NULL DEFAULT '0',
            registration_time   integer unsigned NOT NULL DEFAULT '0',
            modified            integer unsigned NOT NULL DEFAULT '0',
            members             text,
            scm_link            varchar(64) NOT NULL DEFAULT '',
            open_project        tinyint NOT NULL DEFAULT '0',
            extension_type      integer unsigned NOT NULL DEFAULT '1',
            PRIMARY KEY(id),
            KEY i_release_id (name,extension_type),
            KEY i_release_id_rid (extension_id,extension_type)
            )";
    if (!$q->run($query)) {
        return;
    }

    $query = "DROP TABLE IF EXISTS " . $prefix . "_release_notes";
    if (!$q->run($query)) {
        return;
    }
    $query = "CREATE TABLE " . $prefix . "_release_notes (
            id                  integer unsigned NOT NULL auto_increment,
            release_id          integer unsigned NOT NULL DEFAULT '0',
            version             varchar(64) NOT NULL DEFAULT '',
            price               integer unsigned NOT NULL DEFAULT '0',
            price_terms         varchar(255) NOT NULL DEFAULT '',
            demo                tinyint NOT NULL DEFAULT '1',
            demo_link           varchar(255) NOT NULL DEFAULT '',
            dl_link             varchar(255) NOT NULL DEFAULT '',
            supported           tinyint NOT NULL DEFAULT '1',
            support_link        varchar(255) NOT NULL DEFAULT '',
            changelog           text,
            notes               text,
            time                integer unsigned NOT NULL DEFAULT '0',
            enotes              text,
            certified           tinyint NOT NULL DEFAULT '1',
            approved            tinyint NOT NULL DEFAULT '0',
            state               tinyint NOT NULL DEFAULT '0',
            usefeed             tinyint NOT NULL DEFAULT '1',
            extension_type      integer unsigned NOT NULL DEFAULT '1',
            PRIMARY KEY(id),
            KEY i_release_notes_id (release_id)
            )";
    if (!$q->run($query)) {
        return;
    }

    $query = "DROP TABLE IF EXISTS " . $prefix . "_release_docs";
    if (!$q->run($query)) {
        return;
    }
    $query = "CREATE TABLE " . $prefix . "_release_docs (
            id                  integer unsigned NOT NULL auto_increment,
            release_id          integer unsigned NOT NULL DEFAULT '0',
            eid                 integer unsigned NOT NULL DEFAULT '0',
            title               varchar(64) NOT NULL DEFAULT '',
            docs                text,
            extension_type      integer unsigned NOT NULL DEFAULT '0',
            time                integer unsigned NOT NULL DEFAULT '0',
            approved            tinyint NOT NULL DEFAULT '1',
            seq                 tinyint NOT NULL DEFAULT '1',
            PRIMARY KEY(id)
            )";
    if (!$q->run($query)) {
        return;
    }

    # --------------------------------------------------------
#
    # Create DD objects
#
    $module = 'release';
    $objects = array(
                     'release_extensions',
                     'release_notes',
                     'release_docs',
                     );

    if (!xarMod::apiFunc('modules', 'admin', 'standardinstall', array('module' => $module, 'objects' => $objects))) {
        return;
    }
    # --------------------------------------------------------
#
    # Create Base Category
#
    $catName = 'Release';
    try {
        sys::import('modules.categories.class.worker');
        $worker = new CategoryWorker();
        $basecid = $worker->name2id($catName);
    } catch (Exception $e) {
        $basecid = 0;
    }
    if (empty($basecid)) {
        sys::import('modules.dynamicdata.class.objects.master');
        $categories = DataObjectMaster::getObject(array('name' => 'categories'));
        $fieldValues = array(
                'name' => $catName,
                'description' => xarML('Main Release Cats.'),
                'parent_id' => 0,
        );
        $basecid = $categories->createItem($fieldValues);
    }
    
    // Save the base category in a modvar
    xarModVars::set('release', 'mastercids', $basecid);

    # --------------------------------------------------------
#
    # Set up modvars
#
    xarModVars::set('release', 'SupportShortURLs', 0);

    // Register Block types
    if (!xarMod::apiFunc(
        'blocks',
        'admin',
        'register_block_type',
        array('modName'   => 'release',
                             'blockType' => 'latest')
    )) {
        return;
    }
    // Register Block types
    if (!xarMod::apiFunc(
        'blocks',
        'admin',
        'register_block_type',
        array('modName'   => 'release',
                             'blockType' => 'latestprojects')
    )) {
        return;
    }

    # --------------------------------------------------------
#
    # Set up hooks
#

    xarMod::apiFunc(
        'modules',
        'admin',
        'enablehooks',
        array('callerModName' => 'release', 'hookModName' => 'categories')
    );
    // search hook
    if (!xarModHooks::register('item', 'search', 'GUI', 'release', 'user', 'search')) {
        return false;
    }
    # --------------------------------------------------------
#
    # Set up masks
#
    xarMasks::register('ViewRelease', 'All', 'release', 'All', 'All', 'ACCESS_OVERVIEW');
    xarMasks::register('ReadRelease', 'All', 'release', 'All', 'All', 'ACCESS_READ');
    xarMasks::register('EditRelease', 'All', 'release', 'All', 'All', 'ACCESS_EDIT');
    xarMasks::register('AddRelease', 'All', 'release', 'All', 'All', 'ACCESS_ADD');
    xarMasks::register('ManageRelease', 'All', 'release', 'All', 'All', 'ACCESS_DELETE');
    xarMasks::register('AdminRelease', 'All', 'release', 'All', 'All', 'ACCESS_ADMIN');
    xarMasks::register('ReadReleaseBlock', 'All', 'release', 'Block', 'All', 'ACCESS_OVERVIEW');

    return true;
}

/**
 * upgrade the release module from an old version
 * This function can be called multiple times
 */
function release_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
       case '1.0.0': // current version

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
    $module = 'publications';
    return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', array('module' => $module));
}
