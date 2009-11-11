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

    $fields = array(
        'xar_aid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_title'=>array('type'=>'varchar','size'=>254,'null'=>FALSE,'default'=>''),
        'xar_summary'=>array('type'=>'text'),
        'xar_body'=>array('type'=>'text','size'=>'medium'),
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

    // Register blocks
    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'related'))) return;

    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'topitems'))) return;

    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'featureditems'))) return;

    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'random'))) return;

    if (!xarMod::apiFunc('blocks',
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

    /*********************************************************************
    * Define instances for the core modules
    * Format is
    * xarDefineInstance(Module,Component,Querystring,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/
    $xartable = xarDB::getTables();
    $instances = array(
                       array('header' => 'external', // this keyword indicates an external "wizard"
                             'query'  => xarModURL('articles', 'admin', 'privileges'),
                             'limit'  => 0
                            )
                    );
    xarDefineInstance('articles', 'Article', $instances);

// CHECKME: are you sure you want to use the module systemid in blocks, and not the regid ?
    $info = xarMod::getBaseInfo('articles');
    $sysid = $info['systemid'];
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

    xarRegisterMask('ViewArticles','All','articles','Article','All:All:All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadArticles','All','articles','Article','All:All:All:All','ACCESS_READ');
    xarRegisterMask('SubmitArticles','All','articles','Article','All:All:All:All','ACCESS_COMMENT');
// No special meaning here at the moment
//    xarRegisterMask('ModerateArticles','All','articles','Article','All:All:All:All','ACCESS_MODERATE');
    xarRegisterMask('EditArticles','All','articles','Article','All:All:All:All','ACCESS_EDIT');
// Submitting articles only requires COMMENT privileges, not ADD privileges
//    xarRegisterMask('AddArticles','All','articles','Article','All:All:All:All','ACCESS_ADD');
    xarRegisterMask('DeleteArticles','All','articles','Article','All:All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminArticles','All','articles','Article','All:All:All:All','ACCESS_ADMIN');


    xarRegisterMask('ReadArticlesBlock','All','articles','Block','All','ACCESS_READ');

    // Some interesting sample privileges
    xarRegisterPrivilege('Submit Any Articles','All','articles','Article','All:All:All:All','ACCESS_COMMENT', 'Allow people to submit any type of articles');
    xarRegisterPrivilege('Submit News Articles','All','articles','Article','1:All:All:All','ACCESS_COMMENT', 'Allow people to submit only news articles');
    xarRegisterPrivilege('Edit Articles from Myself','All','articles','Article','All:All:Myself:All','ACCESS_EDIT', 'Allow people to edit their own articles');
    xarRegisterPrivilege('Delete Articles in Category 1','All','articles','Article','All:1:All:All','ACCESS_DELETE', 'Allow people to delete articles in a particular category');
    xarRegisterPrivilege('Manage Articles Content','All','articles','Article','All:All:All:All','ACCESS_DELETE', 'Allow people to manage the articles content');
    xarRegisterPrivilege('Manage Articles Configuration','All','articles','Article','All:All:All:All','ACCESS_ADMIN', 'Allow people to manage the articles configuration');

    // Set default settings for publication types
    $settings = array('number_of_columns'    => 0,
                      'itemsperpage'         => 20,
                      'defaultview'          => 1,
                      'showcategories'       => 1,
                      'showcatcount'         => 0,
                      'showprevnext'         => 0,
                      'showcomments'         => 1,
                      'showhitcounts'        => 1,
                      'showratings'          => 0,
                      'showarchives'         => 1,
                      'showmap'              => 1,
                      'showpublinks'         => 0,
                      'showpubcount'         => 1,
                      'dotransform'          => 0,
                      'titletransform'       => 0,
                      'prevnextart'          => 0,
                      'usealias'             => 0,
                      'page_template'        => '',
                      'defaultstatus'        => 0,
                      'defaultsort'          => 'date',
                      'categories'           => array());

    xarModVars::set('articles', 'settings', serialize($settings));
    xarMod::apiFunc('articles','admin','setrootcats',
                    array('ptid' => null,
                          'cids' => null));

    // Load the initial setup of the publication types
    $file = sys::code() . 'modules/articles/xardata/news.xml';
    if (file_exists($file)) {
        $ptid = xarMod::apiFunc('articles','admin','importpubtype',
                                array('file' => $file));
        // default publication type is News Articles
        $defaultpubtype = $ptid;
    } else {
        // TODO: add some defaults here
        $defaultpubtype = 0;
    }

    // Set up module variables
    xarModVars::set('articles', 'SupportShortURLs', 1);

    // Set default publication type
    xarModVars::set('articles', 'defaultpubtype', $defaultpubtype);

    // Enable/disable full-text search with MySQL (for all pubtypes and all text fields)
    xarModVars::set('articles', 'fulltextsearch', '');

    // Allow changing the pubtype names, not recommended
    xarModVars::set('articles', 'ptypenamechange', '');

    // Keep track of checkout editor and time when modifying articles
    xarModVars::set('articles', 'checkout_info', '');

    // Enable articles hooks for search
    if (xarModIsAvailable('search')) {
        xarMod::apiFunc('modules','admin','enablehooks',
                      array('callerModName' => 'search', 'hookModName' => 'articles'));
    }

    // Enable categories hooks for articles
    xarMod::apiFunc('modules','admin','enablehooks',
                  array('callerModName' => 'articles', 'hookModName' => 'categories'));

    // Enable comments hooks for articles
    if (xarModIsAvailable('comments')) {
        xarMod::apiFunc('modules','admin','enablehooks',
                      array('callerModName' => 'articles', 'hookModName' => 'comments'));
    }
    // Enable hitcount hooks for articles
    if (xarModIsAvailable('hitcount')) {
        xarMod::apiFunc('modules','admin','enablehooks',
                      array('callerModName' => 'articles', 'hookModName' => 'hitcount'));
    }
    // Enable ratings hooks for articles
    if (xarModIsAvailable('ratings')) {
        xarMod::apiFunc('modules','admin','enablehooks',
                      array('callerModName' => 'articles', 'hookModName' => 'ratings'));
    }

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
            $pubtypes = xarMod::apiFunc('articles','user','getpubtypes');
            // Get configurable fields for articles
            $pubfields = xarMod::apiFunc('articles','user','getpubfields');
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
                if (!xarMod::apiFunc('articles', 'admin', 'updatepubtype',
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
            if (!xarMod::apiFunc(
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
            // Code to upgrade from version 2.0.0 goes here

            // Get current publication types
            $pubtypes = xarMod::apiFunc('articles','user','getpubtypes');
            // get base categories for all publication types here
            $publist = array_keys($pubtypes);
            // add the defaults too, in case we have other base categories there
            $publist[] = '';
            // build the list of root categories for all required publication types
            foreach ($publist as $pubid) {
                if (empty($pubid)) {
                    $cidstring = xarModVars::get('articles','mastercids');
                } else {
                    $cidstring = xarModVars::get('articles','mastercids'.$pubid);
                }
                if (!empty($cidstring)) {
                    $rootcids = explode(';', $cidstring);
                } else {
                    $rootcids = array();
                }
                // update the base categories for each publication type
                xarMod::apiFunc('articles','admin','setrootcats',
                              array('ptid' => $pubid,
                                    'cids' => $rootcids));
            }

        case '2.0.1':
            // Code to upgrade from version 2.0.1 goes here

            // Get current publication types
            $pubtypes = xarMod::apiFunc('articles','user','getpubtypes');
            // Get configurable fields for articles
            $pubfields = xarMod::apiFunc('articles','user','getpubfields');
            // Update the configuration of each publication type
            foreach ($pubtypes as $ptid => $pubtype) {
                $replace = 0;
                // Update textarea_small format to textarea
                foreach (array_keys($pubfields) as $field) {
                    if ($pubtype['config'][$field]['format'] == 'textarea_small') {
                        $pubtype['config'][$field]['format'] = 'textarea';
                        $replace = 1;
                    }
                }
                if ($replace && !xarMod::apiFunc('articles', 'admin', 'updatepubtype',
                                   array('ptid' => $ptid,
                                         'name' => $pubtype['name'],
                                         'descr' => $pubtype['descr'],
                                         'config' => $pubtype['config']))) {
                    return false;
                }
            }

        case '2.0.2':
            // Code to upgrade from version 2.0.2 goes here
            xarModVars::set('articles', 'checkout_info', '');

        case '2.0.3':
            // Code to upgrade from version 2.0.3 goes here

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

    // Delete dd objects that belong to the articles module
    sys::import('modules.dynamicdata.class.objects.master');
    $objects = DataObjectMaster::getObjects(array('moduleid' => 151));
    foreach ($objects as $objectinfo) {
        // double-check to make sure ;-)
        if ($objectinfo['moduleid'] == 151) {
            DataObjectMaster::deleteObject(array('objectid' => $objectinfo['objectid']));
        }
    }

    // Get current publication types if necessary
    //$pubtypes = xarMod::apiFunc('articles','user','getpubtypes');
    //foreach ($pubtypes as $ptid => $pubtype) {
    //    ... do something per pubtype ...
    //}

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['publication_types']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

// TODO: remove entries from categories_linkage !

    // Delete module variables
    xarModVars::delete_all('articles');

    // UnRegister blocks
    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'related'))) return;

    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'topitems'))) return;

    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'articles',
                             'blockType'=> 'featureditems'))) return;

    if (!xarMod::apiFunc('blocks',
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
