<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * initialise the articles module
 */
function articles_init()
{

    // Get database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    //Load Table Maintainance API
    sys::import('xaraya.tableddl');

// TODO: Somewhere in the future, status should be managed by a workflow module

    // Create tables
    $articlestable = $xartable['articles'];
/*
    $query = "CREATE TABLE $articlestable (
            xar_aid INT(10) NOT NULL AUTO_INCREMENT,
            xar_title VARCHAR(255) NOT NULL DEFAULT '',
            xar_summary TEXT,
            xar_body TEXT,
            xar_notes TEXT,
            xar_status TINYINT(2) NOT NULL DEFAULT '0',
            xar_authorid INT(11) NOT NULL,
            xar_pubdate INT UNSIGNED NOT NULL,
            xar_pubtypeid INT(4) NOT NULL DEFAULT '1',
            xar_pages INT UNSIGNED NOT NULL,
            xar_language VARCHAR(30) NOT NULL DEFAULT '',
            PRIMARY KEY(xar_aid),
            KEY xar_authorid (xar_authorid),
            KEY xar_pubtypeid (xar_pubtypeid),
            KEY xar_pubdate (xar_pubdate),
            KEY xar_status (xar_status)
            )";
*/
    $fields = array(
        'xar_aid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_title'=>array('type'=>'varchar','size'=>254,'null'=>FALSE,'default'=>''),
        'xar_summary'=>array('type'=>'text'),
        'xar_body'=>array('type'=>'text'),
        'xar_notes'=>array('type'=>'text'),
        'xar_status'=>array('type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0'),
        'xar_authorid'=>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        'xar_pubdate'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'),
        'xar_pubtypeid'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'1'),
        'xar_pages'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'1'),
        'xar_language'=>array('type'=>'varchar','size'=>30,'null'=>FALSE,'default'=>'')
    );

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($articlestable,$fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array(
        'name'      => 'i_' . xarDB::getPrefix() . '_articles_authorid',
        'fields'    => array('xar_authorid'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($articlestable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array(
        'name'      => 'i_' . xarDB::getPrefix() . '_articles_pubtypeid',
        'fields'    => array('xar_pubtypeid'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($articlestable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array(
        'name'      => 'i_' . xarDB::getPrefix() . '_articles_pubdate',
        'fields'    => array('xar_pubdate'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($articlestable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array(
        'name'      => 'i_' . xarDB::getPrefix() . '_articles_status',
        'fields'    => array('xar_status'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($articlestable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array(
        'name'      => 'i_' . xarDB::getPrefix() . '_articles_language',
        'fields'    => array('xar_language'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($articlestable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Create tables
    $pubtypestable = $xartable['publication_types'];
/*
    $query = "CREATE TABLE $pubtypestable (
            xar_pubtypeid INT(4) NOT NULL AUTO_INCREMENT,
            xar_pubtypename VARCHAR(30) NOT NULL,
            xar_pubtypedescr VARCHAR(255) NOT NULL DEFAULT '',
            xar_pubtypeconfig TEXT,
            PRIMARY KEY(xar_pubtypeid))";
*/
    $fields = array(
        'xar_pubtypeid'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_pubtypename'=>array('type'=>'varchar','size'=>30,'null'=>FALSE,'default'=>''),
        'xar_pubtypedescr'=>array('type'=>'varchar','size'=>254,'null'=>FALSE,'default'=>''),
        'xar_pubtypeconfig'=>array('type'=>'text')
    );

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($pubtypestable,$fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result =& $dbconn->Execute($query);
    if (!$result) return;

// TODO: load configuration from file(s) ?

    // Load the initial setup of the publication types
    if (file_exists('modules/articles/xarsetup.php')) {
        include 'modules/articles/xarsetup.php';
    } else {
        // TODO: add some defaults here
        $pubtypes = array();
        $categories = array();
        $settings = array();
        $defaultpubtype = 0;
    }

    // Save publication types
    $pubid = array();
    foreach ($pubtypes as $pubtype) {
        list($id,$name,$descr,$config) = $pubtype;
        $nextId = $dbconn->GenId($pubtypestable);
        $query = "INSERT INTO $pubtypestable
                (xar_pubtypeid, xar_pubtypename, xar_pubtypedescr,
                 xar_pubtypeconfig)
                VALUES (?,?,?,?)";
        $bindvars = array($nextId, $name, $descr, $config);
        $result =& $dbconn->Execute($query,$bindvars);
        if (!$result) return;
        $ptid = $dbconn->PO_Insert_ID($pubtypestable, 'xar_pubtypeid');
        $pubid[$id] = $ptid;
    }

    // Create articles categories
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

    // Set up module variables
    xarModVars::set('articles', 'SupportShortURLs', 1);

    // Save articles settings for each publication type
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
                xarModVars::set('articles', 'number_of_categories.'.$id, count($cidlist));
                xarModVars::set('articles', 'mastercids.'.$id, join(';',$cidlist));
            } else {
                xarModVars::set('articles', 'number_of_categories', count($cidlist));
                xarModVars::set('articles', 'mastercids', join(';',$cidlist));
            }
        } elseif (!empty($id)) {
            xarModVars::set('articles', 'number_of_categories.'.$id, 0);
            xarModVars::set('articles', 'mastercids.'.$id, '');
        } else {
            xarModVars::set('articles', 'number_of_categories', 0);
            xarModVars::set('articles', 'mastercids', '');
        }
        if (isset($values['defaultview']) && !is_numeric($values['defaultview'])) {
            if (isset($cid[$values['defaultview']])) {
                $values['defaultview'] = 'c' . $cid[$values['defaultview']];
            } else {
                $values['defaultview'] = 1;
            }
        }
        if (!empty($id)) {
            xarModVars::set('articles', 'settings.'.$id,serialize($values));
        } else {
            xarModVars::set('articles', 'settings',serialize($values));
        }
    }

    // Set default publication type
    xarModVars::set('articles', 'defaultpubtype', $defaultpubtype);

    // Enable/disable full-text search with MySQL (for all pubtypes and all text fields)
    xarModVars::set('articles', 'fulltextsearch', '');

    // Allow changing the pubtype names, not recommended
    xarModVars::set('articles', 'ptypenamechange', '');

    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'related'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'topitems'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'featureditems'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'random'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'glossary'))) return;

    if (!xarModRegisterHook('item', 'search', 'GUI',
                           'articles', 'user', 'search')) {
        return false;
    }

    if (!xarModRegisterHook('item', 'waitingcontent', 'GUI',
                           'articles', 'admin', 'waitingcontent')) {
        return false;
    }

/*
// TODO: move this to some common place in Xaraya (base module ?)
    // Register BL tags
    xarTplRegisterTag('articles', 'articles-field',
                      //array(new xarTemplateAttribute('bid', XAR_TPL_STRING|XAR_TPL_REQUIRED)),
                      array(),
                      'articles_userapi_handleFieldTag');
*/

    // Enable articles hooks for search
    if (xarModIsAvailable('search')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'search', 'hookModName' => 'articles'));
    }

    // Enable categories hooks for articles
/*    xarModAPIFunc('modules','admin','enablehooks',
                  array('callerModName' => 'articles', 'hookModName' => 'categories'));
*/
    // Enable comments hooks for articles
    if (xarModIsAvailable('comments')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'articles', 'hookModName' => 'comments'));
    }
    // Enable hitcount hooks for articles
    if (xarModIsAvailable('hitcount')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'articles', 'hookModName' => 'hitcount'));
    }
    // Enable ratings hooks for articles
    if (xarModIsAvailable('ratings')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'articles', 'hookModName' => 'ratings'));
    }

    /*********************************************************************
    * Define instances for the core modules
    * Format is
    * xarDefineInstance(Module,Component,Querystring,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/
    $info = xarMod::getBaseInfo('articles');
    $sysid = $info['systemid'];
    $xartable = xarDB::getTables();
    $instances = array(
                       array('header' => 'external', // this keyword indicates an external "wizard"
                             'query'  => xarModURL('articles', 'admin', 'privileges'),
                             'limit'  => 0
                            )
                    );
    xarDefineInstance('articles', 'Article', $instances);

    $query = "SELECT DISTINCT instances.xar_title FROM $xartable[block_instances] as instances LEFT JOIN $xartable[block_types] as btypes ON btypes.xar_id = instances.xar_type_id WHERE xar_modid = $sysid";
    $instances = array(
                        array('header' => 'Article Block Title:',
                                'query' => $query,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('articles','Block',$instances);

// TODO: pubtype ?

    /*********************************************************************
    * Register the module components that are privileges objects
    * Format is
    * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    *********************************************************************/

    xarRegisterMask('ViewArticles','All','articles','Article','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadArticles','All','articles','Article','All','ACCESS_READ');
    xarRegisterMask('SubmitArticles','All','articles','Article','All','ACCESS_COMMENT');
// No special meaning here at the moment
//    xarRegisterMask('ModerateArticles','All','articles','Article','All','ACCESS_MODERATE');
    xarRegisterMask('EditArticles','All','articles','Article','All','ACCESS_EDIT');
// Submitting articles only requires COMMENT privileges, not ADD privileges
//    xarRegisterMask('AddArticles','All','articles','Article','All','ACCESS_ADD');
    xarRegisterMask('DeleteArticles','All','articles','Article','All','ACCESS_DELETE');
    xarRegisterMask('AdminArticles','All','articles','Article','All','ACCESS_ADMIN');


    xarRegisterMask('ReadArticlesBlock','All','articles','Block','All','ACCESS_READ');

    // Initialisation successful
    return true;
}

/**
 * upgrade the articles module from an old version
 */
function articles_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '1.4':
            // Get current publication types
            $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
            // Get configurable fields for articles
            $pubfields = xarModAPIFunc('articles','user','getpubfields');
            // Update the configuration of each publication type
            foreach ($pubtypes as $ptid => $pubtype) {
                // Map the (bodytext + bodyfile) fields to a single body field
                // + use the textupload format if relevant
                $pubtype['config']['body'] = $pubtype['config']['bodytext'];
                if (!empty($pubtype['config']['bodyfile']['label'])) {
                    $pubtype['config']['body']['format'] = 'textupload';
                    if (empty($pubtype['config']['body']['label'])) {
                        $pubtype['config']['body']['label'] = $pubtype['config']['bodyfile']['label'];
                    }
                }
                $config = array();
                foreach (array_keys($pubfields) as $field) {
                    $config[$field] = $pubtype['config'][$field];
                }
                if (!xarModAPIFunc('articles', 'admin', 'updatepubtype',
                                   array('ptid' => $ptid,
                                         'name' => $pubtype['name'],
                                         'descr' => $pubtype['descr'],
                                         'config' => $config))) {
                    return false;
                }
            }

        // no upgrade for random block here - you can register it via blocks admin
        case '1.5':
        case '1.5.0':
            // Upgrade the glossary block - we'll be kind :-)
            if (!xarModAPIFunc(
                'blocks', 'admin', 'register_block_type',
                array(
                    'modName'  => 'articles',
                    'blockType'=> 'glossary'
                )
            )) {return;}

        case '1.5.1':
            // Code to upgrade from version 1.5.1 goes here

            // Enable/disable full-text search with MySQL (for all pubtypes and all text fields)
            xarModVars::set('articles', 'fulltextsearch', '');

/* skip for now...
            // Get database information
            $dbconn = xarDB::getConn();
            $xartable = xarDB::getTables();

            //Load Table Maintainance API
            sys::import('xaraya.tableddl');

            $articlestable = $xartable['articles'];

            $index = array(
                'name'      => 'i_' . xarDB::getPrefix() . '_articles_language',
                'fields'    => array('xar_language'),
                'unique'    => false
            );
            $query = xarDBCreateIndex($articlestable,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
*/

        case '1.5.2':
            // Code to upgrade from version 1.5.2 goes here
            xarModVars::set('articles', 'ptypenamechange', '0');

        case '2.0.0':
            // Code to upgrade from version 2.0 goes here

        case '2.5.0':
            // Code to upgrade from version 2.5 goes here
            break;
    }
    return true;
}

/**
 * delete the articles module
 */
function articles_delete()
{
    // Get database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    //Load Table Maintainance API
    sys::import('xaraya.tableddl');

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['articles']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['publication_types']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

// TODO: remove entries from categories_linkage !

    // Delete module variables

    //FIXME: This is breaking the removal of the module...
    xarModVars::delete('articles', 'itemsperpage');

    xarModVars::delete('articles', 'SupportShortURLs');

    xarModVars::delete('articles', 'number_of_categories');
    xarModVars::delete('articles', 'mastercids');

// TODO: remove all current pubtypes

    xarModVars::delete('articles', 'settings.1');
    xarModVars::delete('articles', 'settings.2');
    xarModVars::delete('articles', 'settings.3');
    xarModVars::delete('articles', 'settings.4');
    xarModVars::delete('articles', 'settings.5');
    xarModVars::delete('articles', 'settings.6');

    xarModVars::delete('articles', 'defaultpubtype');
    xarModVars::delete('articles', 'ptypenamechange');

    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'related'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'topitems'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'featureditems'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'glossary'))) return;

/*
// TODO: move this to some common place in Xaraya (base module ?)
    // Unregister BL tags
    xarTplUnregisterTag('articles-field');
*/

    /**
     * Remove instances
     */

    // Remove Masks and Instances
    xarRemoveMasks('articles');
    xarRemoveInstances('articles');


    // Deletion successful
    return true;
}

?>
