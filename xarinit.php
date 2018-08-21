<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * initialise the keywords module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool true on success
 */
sys::import('xaraya.tableddl');
function keywords_init()
{
    $module = 'keywords';

    // Create tables inside transaction
    try {
        $q = new Query();
        $prefix = xarDB::getPrefix();

# --------------------------------------------------------
#
# Table structures
#
        $query = "DROP TABLE IF EXISTS " . $prefix . "_keywords_index";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_keywords_index (
          id                integer unsigned NOT NULL auto_increment,
          module_id         integer unsigned NOT NULL default 0,
          itemtype          integer unsigned NOT NULL default 0,
          itemid            integer unsigned NOT NULL default 0,
          keyword_id        integer unsigned NOT NULL default 0,
          PRIMARY KEY  (id),
          UNIQUE KEY `i_xar_keywords_index` (`module_id`,`itemtype`,`itemid`,`keyword_id`),
          KEY `keyword_id` (`keyword_id`)
        )";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_keywords";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_keywords (
          id                integer unsigned NOT NULL auto_increment,
          index_id          integer unsigned NOT NULL default 0,
          keyword           varchar(64),
          PRIMARY KEY  (id),
          UNIQUE KEY `keyword` (`keyword`),
          KEY `index_id` (`index_id`)
        )";
        if (!$q->run($query)) return;

    } catch (Exception $e) {
        throw new Exception(xarML('Could not create module tables'));
    }

/*********************************************************************
 * Set up Module Vars (common configuration)
 *********************************************************************/

    $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => $module));
    $module_settings->initialize();


/*********************************************************************
 * Set Module Vars (module configuration)
 *********************************************************************/

    xarModVars::set($module, 'delimiters',       ',;');
    xarModVars::set($module, 'stats_per_page',   100);
    xarModVars::set($module, 'items_per_page',   20);
    xarModVars::set($module, 'user_layout',      'list');
    xarModVars::set($module, 'cols_per_page',    2);
    xarModVars::set($module, 'words_per_page',   50);
    xarModVars::set($module, 'cloud_font_min',   1);
    xarModVars::set($module, 'cloud_font_max',   3);
    xarModVars::set($module, 'cloud_font_unit',  'em');
    xarModVars::set($module, 'use_module_icons', true);

/*********************************************************************
 * Create Module DD Objects
 *********************************************************************/

    $objects = array('keywords_keywords');
    if(!xarMod::apiFunc('modules','admin','standardinstall',
        array('module' => $module, 'objects' => $objects))) return;

/*********************************************************************
 * Register Module Hook Observers
 *********************************************************************/

    xarHooks::registerObserver('ItemNew',            $module, 'gui', 'admin', 'newhook');
    xarHooks::registerObserver('ItemCreate',         $module, 'api', 'admin', 'createhook');
    xarHooks::registerObserver('ItemDisplay',        $module, 'gui', 'user',  'displayhook');
    xarHooks::registerObserver('ItemModify',         $module, 'gui', 'admin', 'modifyhook');
    xarHooks::registerObserver('ItemUpdate',         $module, 'api', 'admin', 'updatehook');
    xarHooks::registerObserver('ItemDelete',         $module, 'api', 'admin', 'deletehook');

    xarHooks::registerObserver('ItemSearch',         $module, 'gui', 'user',  'search');

    xarHooks::registerObserver('ModuleModifyconfig', $module, 'gui', 'hooks', 'modulemodifyconfig');
    xarHooks::registerObserver('ModuleUpdateconfig', $module, 'api', 'hooks', 'moduleupdateconfig');
    xarHooks::registerObserver('ModuleRemove',       $module, 'api', 'admin', 'removehook');

/*********************************************************************
 * Define Module Privilege Instances
 *********************************************************************/

    // Defined Instances are: module_id, itemtype and itemid
    $instances = array(
                       array('header' => 'external', // this keyword indicates an external "wizard"
                             'query'  => xarModURL($module, 'admin', 'privileges'),
                             'limit'  => 0
                            )
                    );
    xarDefineInstance($module, 'Item', $instances);


/*********************************************************************
 * Register Module Privilege Masks
 *********************************************************************/

// TODO: tweak this - allow viewing keywords of "your own items" someday ?
// MichelV: Why not have an add privilege in here? Admin to add keywords seems way overdone
    xarRegisterMask('ReadKeywords',   'All', $module, 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditKeywords',   'All', $module, 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddKeywords',    'All', $module, 'Item', 'All:All:All', 'ACCESS_COMMENT');
    xarRegisterMask('ManageKeywords', 'All', $module, 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminKeywords',  'All', $module, 'Item', 'All:All:All', 'ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade the keywords module from an old version
 * This function can be called multiple times
 * @return bool
 */
function keywords_upgrade($oldversion)
{
    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $prefix = xarDB::getPrefix();

    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.0':
        case '1.0.0':

                xarModVars::set('keywords', 'restricted', 0);
                xarModVars::set('keywords', 'default', 'xaraya');

                $dbconn = xarDB::getConn();
                $xartable =& xarDB::getTables();
                $query = xarDBCreateTable($xartable['keywords_restr'],
                             array('id'         => array('type'        => 'integer',
                                                            'null'       => false,
                                                            'increment'  => true,
                                                            'primary_key' => true),
                                   'keyword'    => array('type'        => 'varchar',
                                                            'size'        => 254,
                                                            'null'        => false,
                                                            'default'     => ''),
                                   'module_id'   => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0')
                                  ));

                if (empty($query)) return; // throw back

                // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
                $result = $dbconn->Execute($query);
                if (!$result) return;

                if (!xarModRegisterHook('item', 'search', 'GUI',
                        'keywords', 'user', 'search')) {
                    return;
                }

        case '1.0.2':
            //Alter table restr to add itemtype
            // Get database information
            $dbconn = xarDB::getConn();
            $xartable =& xarDB::getTables();

            // Add column 'itemtype' to table
             $query = xarDBAlterTable($xartable['keywords_restr'],
                                     array('command' => 'add',
                                           'field' => 'itemtype',
                                           'type' => 'integer',
                                           'null' => false,
                                           'default' => '0'));
            $result = & $dbconn->Execute($query);
            if (!$result) return;

            // Register blocks
            if (!xarMod::apiFunc('blocks',
                    'admin',
                    'register_block_type',
                    array('modName'  => 'keywords',
                            'blockType'=> 'keywordsarticles'))) return;
            if (!xarMod::apiFunc('blocks',
                    'admin',
                    'register_block_type',
                    array('modName'  => 'keywords',
                            'blockType'=> 'keywordscategories'))) return;

        case '1.0.3':
            xarModVars::set('keywords', 'useitemtype', 0);

        case '1.0.4':
            xarRegisterMask('AddKeywords', 'All', 'keywords', 'Item', 'All:All:All', 'ACCESS_COMMENT');

        case '1.0.5':
            // upgrade to v2.0.0
            if (!keywords_upgrade_200()) return;

            break;
    }
    // Update successful
    return true;
}

/**
 * delete the keywords module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool true on success
 */
function keywords_delete()
{

    // hooks are removed automatically
    // blocks are removed automatically
    sys::import('xaraya.structures.query');
    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $indextable = $tables['keywords_index'];
    $keywordstable = $tables['keywords'];

    $q = new Query();
    // drop tables
    $query = "DROP TABLE IF EXISTS " . $indextable;
    if (!$q->run($query)) return;
    $query = "DROP TABLE IF EXISTS " . $keywordstable;
    if (!$q->run($query)) return;

    // Remove Masks and Instances
    xarRemoveMasks('keywords');
    xarRemoveInstances('keywords');

    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => 'keywords'));
}

function keywords_upgrade_200()
{
    // upgrade to 2.0.0, normalise tables
    sys::import('xaraya.structures.query');
    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $prefix = xarDB::getPrefix();
    $charset = xarSystemVars::get(sys::CONFIG, 'DB.Charset');
    $indextable = $tables['keywords_index'];
    $keywordstable = $tables['keywords'];
    $restrtable = $tables['keywords_restr'];  // $prefix . '_keywords_restr';

    // Create index table
    try {
        $dbconn->begin();
        $q = new Query();
        // drop table
        $query = "DROP TABLE IF EXISTS " . $indextable;
        if (!$q->run($query)) return;
        //
        // CREATE TABLE {$prefix}_keywords_index (
        //   id         integer NOT NULL auto_increment,
        //   module_id  integer default 0,
        //   itemtype   integer default 0,
        //   itemid     integer default 0
        //   PRIMARY KEY (id)
        // )
        //
        $fields = array(
            'id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
            'module_id' => array('type' => 'integer', 'size' => 11, 'unsigned' => true, 'null' => false, 'default' => '0'),
            'itemtype' => array('type' => 'integer', 'size' => 11, 'unsigned' => true, 'null' => false, 'default' => '0'),
            'itemid' => array('type' => 'integer', 'size' => 11, 'unsigned' => true, 'null' => false, 'default' => '0'),
        );
        // Create the index table
        $query = xarDBCreateTable($indextable, $fields);
        $dbconn->Execute($query);

        // Create indices
        // unique entries
        $index = array(
            'name'   => 'i_'.$prefix.'_keywords_index',
            'fields' => array('module_id', 'itemtype', 'itemid'),
            'unique' => true
        );
        $query = xarDBCreateIndex($indextable,$index);
        $dbconn->Execute($query);
        // Let's commit this, since we're gonna do some other stuff
        $dbconn->commit();

    } catch (Exception $e) {
        $dbconn->rollback();
        throw $e;
    }

    // get all mod, itemtype, itemids from keywords table
    $query = "SELECT module_id, itemtype, itemid
              FROM $keywordstable
              GROUP BY module_id, itemtype, itemid";
    $stmt = $dbconn->prepareStatement($query);
    $result = $stmt->executeQuery(array());

    $values = array();
    $bindvars = array();
    while ($result->next()) {
        $values[] = "(?,?,?)";
        list($module_id, $itemtype, $itemid) = $result->fields;
        $bindvars = array_merge($bindvars, array($module_id, $itemtype, $itemid));
    }
    $result->close();

    // get all mod, itemtype from keywords_restr table
    $query = "SELECT module_id, itemtype
              FROM $restrtable
              GROUP BY module_id, itemtype";
    $stmt = $dbconn->prepareStatement($query);
    $result = $stmt->executeQuery(array());
    while ($result->next()) {
        $values[] = "(?,?,?)";
        list($module_id, $itemtype) = $result->fields;
        $bindvars = array_merge($bindvars, array($module_id, $itemtype, 0));
    }
    $result->close();

    // populate index table
    if (!empty($values)) {
        $insert = "INSERT INTO $indextable (module_id, itemtype, itemid)";
        $insert .= " VALUES " . implode(',',$values);
        try {
            $dbconn->begin();
            $stmt = $dbconn->prepareStatement($insert);
            $stmt->executeUpdate($bindvars);
            $dbconn->commit();
        } catch (SQLException $e) {
            $dbconn->rollback();
            throw $e;
        }
    }

    // get keywords for all module, itemtype, itemids in keywords table
    $query = "SELECT module_id, itemtype, itemid, keyword
              FROM $keywordstable";
    $stmt = $dbconn->prepareStatement($query);
    $result = $stmt->executeQuery(array());

    $keywords = array();
    while ($result->next()) {
        list($module_id, $itemtype, $itemid, $keyword) = $result->fields;
        if (!isset($keywords[$keyword]))
            $keywords[$keyword] = array();
        $keywords[$keyword][] = array('module_id' => $module_id, 'itemtype' => $itemtype, 'itemid' => $itemid);
    }
    $result->close();

    // get keywords for all module, itemtype in keywords_restr table
    $query = "SELECT module_id, itemtype, keyword
              FROM $restrtable";
    $stmt = $dbconn->prepareStatement($query);
    $result = $stmt->executeQuery(array());
    while ($result->next()) {
        list($module_id, $itemtype, $keyword) = $result->fields;
        if (!isset($keywords[$keyword]))
            $keywords[$keyword] = array();
        $keywords[$keyword][] = array('module_id' => $module_id, 'itemtype' => $itemtype, 'itemid' => 0);
    }
    $result->close();

    // (re)Create keywords table
    try {
        $charset = xarSystemVars::get(sys::CONFIG, 'DB.Charset');
        $dbconn->begin();
        $q = new Query();
        // drop keywords table
        $query = "DROP TABLE IF EXISTS " . $keywordstable;
        if (!$q->run($query)) return;
        // drop keywords_restr table
        $query = "DROP TABLE IF EXISTS " . $restrtable;
        if (!$q->run($query)) return;
        //
        // CREATE TABLE {$prefix}_keywords (
        //   id         integer NOT NULL auto_increment,
        //   index_id   integer default 0,
        //   keyword    varchar(254) default ''
        //   PRIMARY KEY (id)
        // )
        //
        $fields = array(
            'id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
            'index_id' => array('type' => 'integer', 'size' => 11, 'unsigned' => true, 'null' => false, 'default' => '0'),
             'keyword' => array('type' => 'varchar', 'size' => 254,'null' => false,'default' => ''),
        );
        // Create the keywords table
        $query = xarDBCreateTable($keywordstable, $fields);
        $dbconn->Execute($query);

        // Create indices
        $index = array(
            'name'   => 'i_'.$prefix.'_keywords_keyword',
            'fields' => array('keyword'),
            'unique' => false
        );
        $query = xarDBCreateIndex($keywordstable,$index);
        $dbconn->Execute($query);
        $index = array(
            'name'   => 'i_'.$prefix.'_keywords_index_id',
            'fields' => array('index_id'),
            'unique' => false
        );
        $query = xarDBCreateIndex($keywordstable,$index);
        $dbconn->Execute($query);
        // Let's commit this, since we're gonna do some other stuff
        $dbconn->commit();

    } catch (Exception $e) {
        $dbconn->rollback();
        throw $e;
    }

    // populate keywords table
    if (!empty($keywords)) {
        // get indexes for all module, itemtype, itemids in index table
        $query = "SELECT id, module_id, itemtype, itemid
                  FROM $indextable";
        $stmt = $dbconn->prepareStatement($query);
        $result = $stmt->executeQuery(array());
        // create hash table of index ids
        while ($result->next()) {
            list($id, $module_id, $itemtype, $itemid) = $result->fields;
            if (!isset($indexes[$module_id]))
                $indexes[$module_id] = array();
            if (!isset($indexes[$module_id][$itemtype]))
                $indexes[$module_id][$itemtype] = array();
            $indexes[$module_id][$itemtype][$itemid] = $id;
        }
        $result->close();

        $values = array();
        $bindvars = array();
        foreach ($keywords as $keyword => $items) {
            foreach ($items as $item) {
                if (isset($indexes[$item['module_id']][$item['itemtype']][$item['itemid']])) {
                    $values[] = '(?,?)';
                    $bindvars[] = $indexes[$item['module_id']][$item['itemtype']][$item['itemid']];
                    $bindvars[] = $keyword;
                }
            }
        }
        // populate keywords table
        if (!empty($values)) {
            $insert = "INSERT INTO $keywordstable (index_id, keyword)";
            $insert .= " VALUES " . implode(',',$values);
            try {
                $dbconn->begin();
                $stmt = $dbconn->prepareStatement($insert);
                $stmt->executeUpdate($bindvars);
                $dbconn->commit();
            } catch (SQLException $e) {
                $dbconn->rollback();
                throw $e;
            }
        }
    }

    // transpose deprecated modvar settings to new format
    $restricted = xarModVars::get('keywords', 'restricted');
    if ($restricted) {
        $useitemtype = xarModVars::get('keywords', 'useitemtype');
        $subjects = xarMod::apiFunc('keywords', 'hooks', 'getsubjects');
        if (!empty($subjects)) {
            foreach (array_keys($subjects) as $hookedto) {
                // get the modules default settings
                $settings = xarMod::apiFunc('keywords', 'hooks', 'getsettings',
                    array(
                        'module' => $hookedto,
                    ));
                // set module default to restricted words
                $settings['restrict_words'] = true;
                if (!$useitemtype) {
                    // not per itemtype, all itemtypes use module default settings
                    $settings['global_config'] = true;
                } else {
                    // per itemtype allowed, set restriction per itemtype
                    if (!empty($subjects[$hookedto]['itemtypes'])) {
                        foreach (array_keys($subjects[$hookedto]['itemtypes']) as $itemtype) {
                            if (empty($itemtype)) continue;
                            $typesettings = xarMod::apiFunc('keywords', 'hooks', 'getsettings',
                                array(
                                    'module' => $hookedto,
                                    'itemtype' => $itemtype,
                                ));
                            $typesettings['restrict_words'] = true;
                            xarMod::apiFunc('keywords', 'hooks', 'updatesettings',
                                array(
                                    'module' => $hookedto,
                                    'itemtype' => $itemtype,
                                    'settings' => $typesettings,
                                ));
                        }
                    }
                }
                xarMod::apiFunc('keywords', 'hooks', 'updatesettings',
                    array(
                        'module' => $hookedto,
                        'settings' => $settings,
                    ));
            }
        }
    }
    xarModVars::delete('keywords', 'restricted');
    xarModVars::delete('keywords', 'useitemtype');

    $cols_per_page = xarModVars::get('keywords', 'displaycolumns',2);
    xarModVars::delete('keywords', 'displaycolumns');

    // new modvars
    xarModVars::set('keywords', 'stats_per_page', 100);
    xarModVars::set('keywords', 'items_per_page', 20);
    xarModVars::set('keywords', 'user_layout', 'list');
    xarModVars::set('keywords', 'cols_per_page', $cols_per_page);
    xarModVars::set('keywords', 'words_per_page', 50);
    xarModVars::set('keywords', 'cloud_font_min', 1);
    xarModVars::set('keywords', 'cloud_font_max', 3);
    xarModVars::set('keywords', 'cloud_font_unit', 'em');
    xarModVars::set('keywords', 'use_module_icons', true);

    xarHooks::registerObserver('ModuleModifyconfig', 'keywords', 'gui', 'hooks', 'modulemodifyconfig');
    xarHooks::registerObserver('ModuleUpdateconfig', 'keywords', 'api', 'hooks', 'moduleupdateconfig');

    return true;
}

?>
