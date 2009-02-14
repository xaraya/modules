<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * initialise the publications module
 */
function publications_init()
{
    // Get database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    //Load Table Maintainance API
    sys::import('xaraya.tableddl');
    sys::import('xaraya.structures.query');

# --------------------------------------------------------
#
# Set tables
#
    $q = new Query();
    $prefix = xarDB::getPrefix();

    $query = "DROP TABLE IF EXISTS " . $prefix . "_publications";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_publications (
            id integer unsigned NOT NULL auto_increment,
            name varchar(64) NOT NULL DEFAULT '',
            title varchar(255) NOT NULL DEFAULT '',
            description TEXT,
            summary TEXT,
            body1 TEXT,
            body2 TEXT,
            body3 TEXT,
            notes TEXT,
            pubtype_id INT(4) NOT NULL DEFAULT '1',
            pages INT UNSIGNED NOT NULL DEFAULT '1',
            locale varchar(20) NOT NULL DEFAULT '',
            start_date integer unsigned NOT NULL,
            end_date integer unsigned NOT NULL,
            owner integer unsigned NULL,
            version integer unsigned NULL,
            create_date integer unsigned NULL,
            modify_date integer unsigned NULL,
            state tinyint NOT NULL DEFAULT '3',
            process_state tinyint NOT NULL DEFAULT '1',
            PRIMARY KEY(id),
            KEY owner (owner),
            KEY pubtype_id (pubtype_id),
            KEY state (state)
            )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_publications_types";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_publications_types (
            id integer unsigned NOT NULL auto_increment,
            name varchar(64) NOT NULL DEFAULT '',
            description varchar(255) NOT NULL DEFAULT '',
            template varchar(255) NOT NULL DEFAULT '',
            configuration TEXT,
            state tinyint unsigned NOT NULL DEFAULT '0',
            PRIMARY KEY(id))";
    if (!$q->run($query)) return;

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
                     );

    if(!xarModAPIFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

        $categories = array();
    // Create publications categories
    $cids = array();
    foreach ($categories as $category) {
        $cid[$category['name']] = xarModAPIFunc('categories',
                                               'admin',
                                               'create',
                        Array('name' => $category['name'],
                              'description' => $category['description'],
                              'parent_id' => 0));
        foreach ($category['children'] as $child) {
            $cid[$child] = xarModAPIFunc('categories',
                                        'admin',
                                        'create',
                        Array('name' => $child,
                              'description' => $child,
                              'parent_id' => $cid[$category['name']]));
        }
    }

# --------------------------------------------------------
#
# Set up modvars
#
    xarModVars::set('publications', 'itemsperpage', 20);
    xarModVars::set('publications', 'useModuleAlias',0);
    xarModVars::set('publications', 'aliasname','Publications');
    xarModVars::set('publications', 'defaultmastertable','publications_documents');
    xarModVars::set('publications', 'SupportShortURLs', 1);
    xarModVars::set('publications', 'fulltextsearch', '');
    xarModVars::set('publications', 'defaultpubtype', 10);

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
    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'publications',
                             'blockType'=> 'related'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'publications',
                             'blockType'=> 'topitems'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'publications',
                             'blockType'=> 'featureditems'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'publications',
                             'blockType'=> 'random'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'publications',
                             'blockType'=> 'glossary'))) return;

    if (!xarModRegisterHook('item', 'search', 'GUI',
                           'publications', 'user', 'search')) {
        return false;
    }

    if (!xarModRegisterHook('item', 'waitingcontent', 'GUI',
                           'publications', 'admin', 'waitingcontent')) {
        return false;
    }

// TODO: move this to some common place in Xaraya (base module ?)
    // Register BL tags
    xarTplRegisterTag('publications', 'publications-field',
                      //array(new xarTemplateAttribute('bid', XAR_TPL_STRING|XAR_TPL_REQUIRED)),
                      array(),
                      'publications_userapi_handlefieldtag');

# --------------------------------------------------------
#
# Set up hooks
#
    sys::import('xaraya.structures.hooks.observer');

    $observer = new BasicObserver('publications','admin','getconfighook');
    $observer->register('module', 'getconfig', 'API');
    $subject = new HookSubject('listings');
    $subject->attach($observer);

    // Enable publications hooks for search
    if (xarModIsAvailable('search')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'search', 'hookModName' => 'publications'));
    }

    // Enable categories hooks for publications
/*    xarModAPIFunc('modules','admin','enablehooks',
                  array('callerModName' => 'publications', 'hookModName' => 'categories'));
*/
    // Enable comments hooks for publications
    if (xarModIsAvailable('comments')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'publications', 'hookModName' => 'comments'));
    }
    // Enable hitcount hooks for publications
    if (xarModIsAvailable('hitcount')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'publications', 'hookModName' => 'hitcount'));
    }
    // Enable ratings hooks for publications
    if (xarModIsAvailable('ratings')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'publications', 'hookModName' => 'ratings'));
    }

    /*********************************************************************
    * Define instances for the core modules
    * Format is
    * xarDefineInstance(Module,Component,Querystring,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/
    $info = xarMod::getBaseInfo('publications');
    $sysid = $info['systemid'];
    $xartable = xarDB::getTables();
    $instances = array(
                       array('header' => 'external', // this keyword indicates an external "wizard"
                             'query'  => xarModURL('publications', 'admin', 'privileges'),
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
    xarDefineInstance('publications','Block',$instances);

# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewPublications','All','publications','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadPublications','All','publications','All','All','ACCESS_READ');
    xarRegisterMask('SubmitPublications','All','publications','All','All','ACCESS_COMMENT');
    xarRegisterMask('ModeratePublications','All','publications','All','All','ACCESS_MODERATE');
    xarRegisterMask('EditPublications','All','publications','All','All','ACCESS_EDIT');
    xarRegisterMask('AddPublications','All','publications','All','All','ACCESS_ADD');
    xarRegisterMask('ManagePublications','All','publications','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminPublications','All','publications','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('ViewPublications','All','publications','All','All','ACCESS_OVERVIEW');
    xarRegisterPrivilege('ReadPublications','All','publications','All','All','ACCESS_READ');
    xarRegisterPrivilege('SubmitPublications','All','publications','All','All','ACCESS_COMMENT');
    xarRegisterPrivilege('ModeratePublications','All','publications','All','All','ACCESS_MODERATE');
    xarRegisterPrivilege('EditPublications','All','publications','All','All','ACCESS_EDIT');
    xarRegisterPrivilege('AddPublications','All','publications','All','All','ACCESS_ADD');
    xarRegisterPrivilege('ManagePublications','All','publications','All','All','ACCESS_DELETE');
    xarRegisterPrivilege('AdminPublications','All','publications','All','All','ACCESS_ADMIN');

    xarRegisterMask('ReadPublicationsBlock','All','publications','Block','All','ACCESS_READ');

    // Initialisation successful
    return true;
}

/**
 * upgrade the publications module from an old version
 */
function publications_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {

        case '2.0.0':
            // Code to upgrade from version 2.0 goes here

        case '2.5.0':
            // Code to upgrade from version 2.5 goes here
            break;
    }
    return true;
}

/**
 * delete the publications module
 */
function publications_delete()
{
    $module = 'publications';
    return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => $module));

   // TODO: remove everything below here
   // Get database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    //Load Table Maintainance API
    sys::import('xaraya.tableddl');

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['publications']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['publications_types']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

// TODO: remove entries from categories_linkage !

    // Delete module variables

    //FIXME: This is breaking the removal of the module...
    xarModVars::delete('publications', 'itemsperpage');

    xarModVars::delete('publications', 'SupportShortURLs');

    xarModVars::delete('publications', 'number_of_categories');
    xarModVars::delete('publications', 'mastercids');

// TODO: remove all current pubtypes

    xarModVars::delete('publications', 'settings.1');
    xarModVars::delete('publications', 'settings.2');
    xarModVars::delete('publications', 'settings.3');
    xarModVars::delete('publications', 'settings.4');
    xarModVars::delete('publications', 'settings.5');
    xarModVars::delete('publications', 'settings.6');

    xarModVars::delete('publications', 'defaultpubtype');

    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'publications',
                             'blockType'=> 'related'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'publications',
                             'blockType'=> 'topitems'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'publications',
                             'blockType'=> 'featureditems'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'publications',
                             'blockType'=> 'glossary'))) return;

// TODO: move this to some common place in Xaraya (base module ?)
    // Unregister BL tags
    xarTplUnregisterTag('publications-field');

    /**
     * Remove instances
     */

    // Remove Masks and Instances
    xarRemoveMasks('publications');
    xarRemoveInstances('publications');


    // Deletion successful
    return true;
}

?>
