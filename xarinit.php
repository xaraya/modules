<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */

/**
 * Initialise the categories module
 *
 * @author  Jim McDonald, Fl?vio Botelho <nuncanada@xaraya.com>, mikespub <postnuke@mikespub.net>
 * @access  public
 * @param   none
 * @return  true on success or void or false on failure
 * @throws  'DATABASE_ERROR'
 * @todo    nothing
*/
function categories_init()
{
    //Load Table Maintainance API
    xarDBLoadTableMaintenanceAPI();

    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    /* CREATE TABLE xar_categories (
     *  xar_cid          int(11) NOT NULL auto_increment,
     *  xar_name         varchar(64) NOT NULL,
     *  xar_description  varchar(255) NOT NULL,
     *  xar_image        varchar(255) NOT NULL,
     *  xar_parent       int(11) NOT NULL default 0,
     *  xar_left         int(11) unsigned NOT NULL,
     *  xar_right        int(11) unsigned NOT NULL,
     *  PRIMARY KEY (xar_cid),
     *  KEY xar_left (xar_left),
     *  KEY xar_right (xar_right),
     *  KEY xar_parent (xar_parent)
     * )
    **/

    $fields = array(
        'xar_cid'         => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
        'xar_name'        => array('type'=>'varchar','size'=>64,'null'=>false),
        'xar_description' => array('type'=>'varchar','size'=>255,'null'=>false),
        'xar_image'       => array('type'=>'varchar','size'=>255,'null'=>false),
        'xar_parent'      => array('type'=>'integer','null'=>false,'default'=>'0'),
        'xar_left'        => array('type'=>'integer','null'=>false,'unsigned'=>true),
        'xar_right'       => array('type'=>'integer','null'=>false,'unsigned'=>true)
    );
    $query = xarDBCreateTable($xartable['categories'],$fields);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_cat_left',
                   'fields'    => array('xar_left'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['categories'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_cat_right',
                   'fields'    => array('xar_right'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['categories'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_cat_parent',
                   'fields'    => array('xar_parent'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['categories'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    /* CREATE TABLE $categorieslinkagetable (
     *  cid int(11) NOT NULL,
     *  iid int(11) NOT NULL,
     *  modid int(11) NOT NULL,
     *  KEY xar_iid (xar_iid),
     *  KEY xar_cid (xar_cid),
     *  KEY xar_modid (xar_modid)
     * )
    **/

    $fields = array(
        'xar_cid'   => array('type'=>'integer','null'=>false),
        'xar_iid'   => array('type'=>'integer','null'=>false),
        'xar_modid' => array('type'=>'integer','null'=>false),
        'xar_itemtype' => array('type'=>'integer','null'=>false)
    );
    $query = xarDBCreateTable($xartable['categories_linkage'],$fields);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_cat_linkage_1',
                   'fields'    => array('xar_cid'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['categories_linkage'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_cat_linkage_2',
                   'fields'    => array('xar_iid'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['categories_linkage'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_cat_linkage_3',
                   'fields'    => array('xar_modid'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['categories_linkage'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_cat_linkage_4',
                   'fields'    => array('xar_itemtype'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['categories_linkage'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Set up module variables
//    xarModSetVar('categories', 'bold', 0);
    xarModSetVar('categories', 'catsperpage', 40);
    xarModSetVar('categories', 'usename', false);
    // when a new module item is being specified
    if (!xarModRegisterHook('item', 'new', 'GUI',
                           'categories', 'admin', 'newhook')) {
        return false;
    }
    // when a module item is created (uses 'cids')
    if (!xarModRegisterHook('item', 'create', 'API',
                           'categories', 'admin', 'createhook')) {
        return false;
    }
    // when a module item is being modified (uses 'cids')
    if (!xarModRegisterHook('item', 'modify', 'GUI',
                           'categories', 'admin', 'modifyhook')) {
        return false;
    }
    // when a module item is updated (uses 'cids')
    if (!xarModRegisterHook('item', 'update', 'API',
                           'categories', 'admin', 'updatehook')) {
        return false;
    }
    // when a module item is deleted
    if (!xarModRegisterHook('item', 'delete', 'API',
                           'categories', 'admin', 'deletehook')) {
        return false;
    }
    // when a module configuration is being modified (uses 'cids')
    if (!xarModRegisterHook('module', 'modifyconfig', 'GUI',
                           'categories', 'admin', 'modifyconfighook')) {
        return false;
    }
    // when a module configuration is updated (uses 'cids')
    if (!xarModRegisterHook('module', 'updateconfig', 'API',
                           'categories', 'admin', 'updateconfighook')) {
        return false;
    }
    // when a whole module is removed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModRegisterHook('module', 'remove', 'API',
                           'categories', 'admin', 'removehook')) {
        return false;
    }

    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'categories',
                             'blockType'=> 'navigation'))) return;

    // Register BL tags
    xarTplRegisterTag('categories', 'categories-navigation',
                      array(),
                      'categories_userapi_navigationtag');
    xarTplRegisterTag('categories', 'categories-filter',
                      array(),
                      'categories_userapi_filtertag');

    xarTplRegisterTag('categories', 'categories-catinfo', array(),
                      'categories_userapi_getcatinfotag');
            // fall through to the next upgrade
    /*********************************************************************
    * Define instances for this module
    * Format is
    * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/
    $xartable =& xarDBGetTables();
    $categorytable =$xartable['categories'];
/*
    $query1 = "SELECT DISTINCT xar_name FROM ".$categorytable;
    $query2 = "SELECT DISTINCT xar_cid FROM ".$categorytable;
    $instances = array(
                        array('header' => 'Category Name:',
                                'query' => $query1,
                                'limit' => 20
                            ),
                        array('header' => 'Category ID:',
                                'query' => $query2,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('categories','Category',$instances,1,$categorytable,'xar_cid',
    'xar_parent','Instances of the categories module, including multilevel nesting');
*/
    $query = "SELECT DISTINCT instances.xar_title FROM $xartable[block_instances] as instances LEFT JOIN $xartable[block_types] as btypes ON btypes.xar_id = instances.xar_type_id WHERE xar_module = 'categories'";
    $instances = array(
                        array('header' => 'Category Block Title:',
                                'query' => $query,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('categories','Block',$instances);

    // use external privilege wizard for 'Category' and 'Link' instances
    $instances = array(
                       array('header' => 'external', // this keyword indicates an external "wizard"
                             'query'  => xarModURL('categories', 'admin', 'privileges'),
                             'limit'  => 0
                            )
                    );
    xarDefineInstance('categories', 'Link', $instances);
// TODO: get this parent/child stuff to work someday, or implement some other way ?
    //xarDefineInstance('categories', 'Category', $instances);
    xarDefineInstance('categories', 'Category', $instances,1,$categorytable,'xar_cid',
    'xar_parent','Instances of the categories module, including multilevel nesting');


    /*********************************************************************
    * Register the module components that are privileges objects
    * Format is
    * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    *********************************************************************/

    xarRegisterMask('ViewCategories','All','categories','Category','All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadCategories','All','categories','Category','All:All','ACCESS_READ');
    xarRegisterMask('EditCategories','All','categories','Category','All:All','ACCESS_EDIT');
    xarRegisterMask('AddCategories','All','categories','Category','All:All','ACCESS_ADD');
    xarRegisterMask('DeleteCategories','All','categories','Category','All:All','ACCESS_DELETE');
    xarRegisterMask('AdminCategories','All','categories','Category','All:All','ACCESS_ADMIN');

    xarRegisterMask('ReadCategoryBlock','All','categories','Block','All:All:All','ACCESS_READ');

    xarRegisterMask('ViewCategoryLink','All','categories','Link','All:All:All:All','ACCESS_OVERVIEW');
    xarRegisterMask('SubmitCategoryLink','All','categories','Link','All:All:All:All','ACCESS_COMMENT');
    xarRegisterMask('EditCategoryLink','All','categories','Link','All:All:All:All','ACCESS_EDIT');
    xarRegisterMask('DeleteCategoryLink','All','categories','Link','All:All:All:All','ACCESS_DELETE');

    // Initialisation successful
    return true;
}

/**
 * Upgrade the categories module from an old version
 *
 * @author  Jim McDonald, Fl?vio Botelho <nuncanada@xaraya.com>, mikespub <postnuke@mikespub.net>
 * @access  public
 * @param   $oldVersion
 * @return  true on success or false on failure
 * @throws  no exceptions
 * @todo    nothing
*/
function categories_upgrade($oldversion)
{
    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Upgrade dependent on old version number
    switch($oldversion) {
        case '1.0':
            // Code to upgrade from version 1.0 goes here
            // fall through to the next upgrade

        case '2.0':
            // Code to upgrade from version 2.0 goes here

        // TODO: remove this for release
            $query = "ALTER TABLE $xartable[categories]
                      ADD COLUMN xar_image varchar(255) NOT NULL";
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            // fall through to the next upgrade

        case '2.1':
            // Code to upgrade from version 2.1 goes here

        // TODO: remove this for release

            // when a new module item is being specified
            if (!xarModRegisterHook('item', 'new', 'GUI',
                                   'categories', 'admin', 'newhook')) {
                return false;
            }
            // when a module item is created (uses 'cids')
            if (!xarModRegisterHook('item', 'create', 'API',
                                   'categories', 'admin', 'createhook')) {
                return false;
            }
            // when a module item is being modified (uses 'cids')
            if (!xarModRegisterHook('item', 'modify', 'GUI',
                                   'categories', 'admin', 'modifyhook')) {
                return false;
            }
            // when a module item is updated (uses 'cids')
            if (!xarModRegisterHook('item', 'update', 'API',
                                   'categories', 'admin', 'updatehook')) {
                return false;
            }
            // when a module item is deleted
            if (!xarModRegisterHook('item', 'delete', 'API',
                                   'categories', 'admin', 'deletehook')) {
                return false;
            }
            // when a module configuration is being modified (uses 'cids')
            if (!xarModRegisterHook('module', 'modifyconfig', 'GUI',
                                   'categories', 'admin', 'modifyconfighook')) {
                return false;
            }
            // when a module configuration is updated (uses 'cids')
            if (!xarModRegisterHook('module', 'updateconfig', 'API',
                                   'categories', 'admin', 'updateconfighook')) {
                return false;
            }
            // when a whole module is removed, e.g. via the modules admin screen
            // (set object ID to the module name !)
            if (!xarModRegisterHook('module', 'remove', 'API',
                                   'categories', 'admin', 'removehook')) {
                return false;
            }
            // fall through to the next upgrade

        case '2.2':
            // Code to upgrade from version 2.2 goes here

            if (xarModIsAvailable('articles')) {
                // load API for table definition etc.
                if (!xarModAPILoad('articles','user')) return;
            }

            $linkagetable = $xartable['categories_linkage'];

            xarDBLoadTableMaintenanceAPI();

            // add the xar_itemtype column
            $query = xarDBAlterTable($linkagetable,
                                     array('command' => 'add',
                                           'field' => 'xar_itemtype',
                                           'type' => 'integer',
                                           'null' => false,
                                           'default' => '0'));
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            // make sure all current records have an itemtype 0 (just in case)
            $query = "UPDATE $linkagetable SET xar_itemtype = 0";
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // update the itemtype field for all articles
            if (xarModIsAvailable('articles')) {
                $modid = xarModGetIDFromName('articles');
                $articlestable = $xartable['articles'];

                $query = "SELECT xar_aid, xar_pubtypeid FROM $articlestable";
                $result =& $dbconn->Execute($query);
                if (!$result) return;

                while (!$result->EOF) {
                    list($aid,$ptid) = $result->fields;
                    $update = "UPDATE $linkagetable SET xar_itemtype = $ptid WHERE xar_iid = $aid AND xar_modid = $modid";
                    $test =& $dbconn->Execute($update);
                    if (!$test) return;

                    $result->MoveNext();
                }
                $result->Close();
            }

// TODO: any other modules where we need to insert the right itemtype here ?

            // add an index for the xar_itemtype column
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_cat_linkage_4',
                           'fields'    => array('xar_itemtype'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($linkagetable,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // fall through to the next upgrade

        case '2.3':
        case '2.3.0':
            // remove old instance definitions for 'Category'
            $instancetable = $xartable['security_instances'];
            $query = "DELETE FROM $instancetable
                      WHERE xar_module='categories' AND xar_component='Category'";
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            $categorytable =$xartable['categories'];
            // use external privilege wizard for 'Category' instance
            $instances = array(
                               array('header' => 'external', // this keyword indicates an external "wizard"
                                     'query'  => xarModURL('categories', 'admin', 'privileges'),
                                     'limit'  => 0
                                    )
                              );
        // TODO: get this parent/child stuff to work someday, or implement some other way ?
            //xarDefineInstance('categories', 'Category', $instances);
            xarDefineInstance('categories', 'Category', $instances,1,$categorytable,'xar_cid',
            'xar_parent','Instances of the categories module, including multilevel nesting');

            // fall through to the next upgrade

        case '2.3.1':
            xarTplRegisterTag('categories', 'categories-filter',
                              array(),
                              'categories_userapi_filtertag');

            // fall through to the next upgrade

        case '2.3.2':
            xarTplRegisterTag('categories', 'categories-catinfo', array(),
                      'categories_userapi_getcatinfotag');
            // fall through to the next upgrade
        case '2.3.3':
            // fall through to the next upgrade
        case '2.5.0':
            // Code to upgrade from version 2.5 goes here
            break;
    }

    // Upgrade successful
    return true;
}

/**
 * Delete the categories module
 *
 * @author  Jim McDonald, Fl?vio Botelho <nuncanada@xaraya.com>, mikespub <postnuke@mikespub.net>
 * @access  public
 * @param   no parameters
 * @return  true on success or false on failure
 * @todo    restore the default behaviour prior to 1.0 release
*/
function categories_delete()
{
    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Delete categories table
    $query = "DROP TABLE ".$xartable['categories'];
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Delete links table
    $query = "DROP TABLE ".$xartable['categories_linkage'];
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Delete module variables
//    xarModDelVar('categories', 'bold');
    xarModDelVar('categories', 'catsperpage');

    // Remove module hooks
    if (!xarModUnregisterHook('item', 'new', 'GUI',
                             'categories', 'admin', 'newhook')) return;
    if (!xarModUnregisterHook('item', 'create', 'API',
                             'categories', 'admin', 'createhook')) return;
    if (!xarModUnregisterHook('item', 'modify', 'GUI',
                             'categories', 'admin', 'modifyhook')) return;
    if (!xarModUnregisterHook('item', 'update', 'API',
                             'categories', 'admin', 'updatehook'))return;
    if (!xarModUnregisterHook('item', 'delete', 'API',
                             'categories', 'admin', 'deletehook'))return;
    if (!xarModUnregisterHook('module', 'modifyconfig', 'GUI',
                             'categories', 'admin', 'modifyconfighook'))return;
    if (!xarModUnregisterHook('module', 'updateconfig', 'API',
                             'categories', 'admin', 'updateconfighook'))return;
    if (!xarModUnregisterHook('module', 'remove', 'API',
                             'categories', 'admin', 'removehook')) return;

    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'categories',
                             'blockType'=> 'navigation'))) return;

    xarTplUnregisterTag('categories-navigation');
    xarTplUnregisterTag('categories-filter');
    xarTplUnregisterTag('categories-catinfo');
    /**
     * Remove instances and masks
     */

    // Remove Masks and Instances
    xarRemoveMasks('categories');
    xarRemoveInstances('categories');

    // Deletion successful
    return true;
}

?>
