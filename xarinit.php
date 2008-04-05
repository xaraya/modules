<?php
/**
 * File: $Id: s.xarinit.php 1.22 03/01/26 20:03:00-05:00 John.Cox@mcnabb. $
 *
 * Categories System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage categories module
 * @author Jim McDonald, Flávio Botelho <nuncanada@xaraya.com>, mikespub <postnuke@mikespub.net>
*/

//Load Table Maintainance API
sys::import('xaraya.tableddl');

/**
 * Initialise the categories module
 *
 * @author  Jim McDonald, Flávio Botelho <nuncanada@xaraya.com>, mikespub <postnuke@mikespub.net>
 * @access  public
 * @param   none
 * @return  true on success or void or false on failure
 * @throws  'DATABASE_ERROR'
 * @todo    nothing
*/
function categories_init()
{
    xarTemplateTag::unregisterall('categories');
    // Get database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $prefix = xarDB::getPrefix();

    $fields = array(
        'id'         => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
        'name'        => array('type'=>'varchar','size'=>64,'null'=>false),
        'description' => array('type'=>'varchar','size'=>255,'null'=>false),
        'image'       => array('type'=>'varchar','size'=>255,'null'=>false),
        'parent_id'   => array('type'=>'integer','null'=>false,'default'=>'0'),
        'left_id'     => array('type'=>'integer','null'=>true,'unsigned'=>true),
        'right_id'    => array('type'=>'integer','null'=>true,'unsigned'=>true),
        'state'       => array('type'=>'integer','null'=>false,'default'=>'3')
    );
    $query = xarDBCreateTable($xartable['categories'],$fields);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . $prefix . '_left_id',
                   'fields'    => array('left_id'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['categories'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . $prefix . '_right_id',
                   'fields'    => array('right_id'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['categories'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . $prefix . '_parent_id',
                   'fields'    => array('parent_id'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['categories'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $fields = array(
        'category_id'   => array('type'=>'integer','null'=>false),
        'item_id'   => array('type'=>'integer','null'=>false),
        'module_id' => array('type'=>'integer','null'=>false),
        'itemtype' => array('type'=>'integer','null'=>false),
        'basecategory' => array('type'=>'integer','null'=>false)
    );
    $query = xarDBCreateTable($xartable['categories_linkage'],$fields);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . $prefix . '_cat_linkage_1',
                   'fields'    => array('category_id'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['categories_linkage'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . $prefix . '_cat_linkage_2',
                   'fields'    => array('item_id'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['categories_linkage'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . $prefix . '_cat_linkage_3',
                   'fields'    => array('module_id'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['categories_linkage'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . $prefix . '_cat_linkage_4',
                   'fields'    => array('itemtype'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['categories_linkage'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    # --------------------------------------------------

    /* Don't implement for now
    $q = new xarQuery();
    $query = "DROP TABLE IF EXISTS " . $prefix . "_categories_linkage_summary";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_categories_linkage_summary (
      category_id int(11) DEFAULT NULL,
      module_id int(11) DEFAULT NULL,
      itemtype int(11) DEFAULT NULL,
      links int(11) DEFAULT NULL,
      PRIMARY KEY  (category_id)
    )";
    if (!$q->run($query)) return;
    */

    $q = new xarQuery();
    $query = "DROP TABLE IF EXISTS " . $prefix . "_categories_basecategories";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_categories_basecategories (
      id int NOT NULL auto_increment,
      category_id int(11) DEFAULT '1' NOT NULL,
      module_id int(11) DEFAULT NULL,
      itemtype int(11) DEFAULT NULL,
      name varchar(64) NOT NULL,
      selectable int(1) DEFAULT '1' NOT NULL,
      PRIMARY KEY  (id)
    )";
    if (!$q->run($query)) return;

    // Set up module variables
//    xarModVars::set('categories', 'bold', 0);
    xarModVars::set('categories', 'catsperpage', 40);

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

    // Not a good idea. Leads to duplicate entries for dd objects based on categories
    /*
        xarModAPIFunc('modules','admin','enablehooks',
                  array('callerModName' => 'categories', 'hookModName' => 'dynamicdata'));
    */
    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'categories',
                             'blockType'=> 'navigation'))) return;

    /*********************************************************************
    * Define instances for this module
    * Format is
    * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/
    $xartable = xarDB::getTables();
    $categorytable =$xartable['categories'];
/*
    $query1 = "SELECT DISTINCT name FROM ".$categorytable;
    $query2 = "SELECT DISTINCT id FROM ".$categorytable;
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
    xarDefineInstance('categories','Category',$instances,1,$categorytable,'id',
    'parent_id','Instances of the categories module, including multilevel nesting');
*/
    $info = xarMod::getBaseInfo('categories');
    $sysid = $info['systemid'];
    $query = "SELECT DISTINCT instances.title FROM $xartable[block_instances] as instances LEFT JOIN $xartable[block_types] as btypes ON btypes.id = instances.type_id WHERE module_id = $sysid";
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
    xarDefineInstance('categories', 'Category', $instances,1,$categorytable,'id',
    'parent_id','Instances of the categories module, including multilevel nesting');


    /*********************************************************************
    * Register the module components that are privileges objects
    * Format is
    * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    *********************************************************************/

    xarRegisterMask('ViewCategories','All','categories','Category','All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadCategories','All','categories','Category','All:All','ACCESS_READ');
    xarRegisterMask('CommmentCategories','All','categories','Category','All:All','ACCESS_COMMENT');
    xarRegisterMask('ModerateCategories','All','categories','Category','All:All','ACCESS_MODERATE');
    xarRegisterMask('EditCategories','All','categories','Category','All:All','ACCESS_EDIT');
    xarRegisterMask('AddCategories','All','categories','Category','All:All','ACCESS_ADD');
    xarRegisterMask('ManageCategories','All','categories','Category','All:All','ACCESS_DELETE');
    xarRegisterMask('AdminCategories','All','categories','Category','All:All','ACCESS_ADMIN');

    xarRegisterMask('ReadCategoryBlock','All','categories','Block','All:All:All','ACCESS_READ');

    xarRegisterMask('ViewCategoryLink','All','categories','Link','All:All:All:All','ACCESS_OVERVIEW');
    xarRegisterMask('SubmitCategoryLink','All','categories','Link','All:All:All:All','ACCESS_COMMENT');
    xarRegisterMask('EditCategoryLink','All','categories','Link','All:All:All:All','ACCESS_EDIT');
    xarRegisterMask('DeleteCategoryLink','All','categories','Link','All:All:All:All','ACCESS_DELETE');

    xarRegisterPrivilege('ViewCategories','All','categories','Category','All','ACCESS_OVERVIEW');
    xarRegisterPrivilege('ReadCategories','All','categories','Category','All','ACCESS_READ');
    xarRegisterPrivilege('CommmentCategories','All','categories','Category','All','ACCESS_COMMENT');
    xarRegisterPrivilege('ModerateCategories','All','categories','Category','All','ACCESS_MODERATE');
    xarRegisterPrivilege('EditCategories','All','categories','Category','All','ACCESS_EDIT');
    xarRegisterPrivilege('AddCategories','All','categories','Category','All','ACCESS_ADD');
    xarRegisterPrivilege('ManageCategories','All','categories','Category','All:All','ACCESS_DELETE');
    xarRegisterPrivilege('AdminCategories','All','categories','Category','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up modvars
#
    xarModVars::set('categories', 'itemsperpage', 20);
    xarModVars::set('categories', 'usejsdisplay', 0);
    xarModVars::set('categories', 'numstats', 100);
    xarModVars::set('categories', 'showtitle', 1);
    xarModVars::set('categories', 'categoriesobject', 'categories');

# --------------------------------------------------------
#
# Create DD objects
#
    PropertyRegistration::importPropertyTypes(false,array('modules/categories/xarproperties'));

    $module = 'categories';
    $objects = array(
                     'categories',
                     );
    if(!xarModAPIFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

    // Initialisation successful
    return true;
}

/**
 * Upgrade the categories module from an old version
 *
 * @author  Jim McDonald, Flávio Botelho <nuncanada@xaraya.com>, mikespub <postnuke@mikespub.net>
 * @access  public
 * @param   $oldVersion
 * @return  true on success or false on failure
 * @throws  no exceptions
 * @todo    nothing
*/
function categories_upgrade($oldversion)
{
    // Get database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    // Upgrade dependent on old version number
    switch($oldversion) {
        case '1.0':
            // Code to upgrade from version 1.0 goes here
            // fall through to the next upgrade

        case '2.0':
            // Code to upgrade from version 2.0 goes here

        // TODO: remove this for release
            $query = "ALTER TABLE $xartable[categories]
                      ADD COLUMN image varchar(255) NOT NULL";
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

            sys::import('xaraya.tableddl');

            // add the itemtype column
            $query = xarDBAlterTable($linkagetable,
                                     array('command' => 'add',
                                           'field' => 'itemtype',
                                           'type' => 'integer',
                                           'null' => false,
                                           'default' => '0'));
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            // make sure all current records have an itemtype 0 (just in case)
            $query = "UPDATE $linkagetable SET itemtype = 0";
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // update the itemtype field for all articles
            if (xarModIsAvailable('articles')) {
                $modid = xarMod::getID('articles');
                $articlestable = $xartable['articles'];

                $query = "SELECT xar_aid, xar_pubtypeid FROM $articlestable";
                $result =& $dbconn->Execute($query);
                if (!$result) return;

                while (!$result->EOF) {
                    list($aid,$ptid) = $result->fields;
                    $update = "UPDATE $linkagetable SET itemtype = $ptid WHERE item_id = $aid AND module_id = $modid";
                    $test =& $dbconn->Execute($update);
                    if (!$test) return;

                    $result->MoveNext();
                }
                $result->Close();
            }

// TODO: any other modules where we need to insert the right itemtype here ?

            // add an index for the itemtype column
            $index = array('name'      => 'i_' . $prefix . '_cat_linkage_4',
                           'fields'    => array('itemtype'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($linkagetable,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // fall through to the next upgrade

        case '2.3':
        case '2.3.0':
            $info = xarMod::getBaseInfo('categories');
            $sysid = $info['systemid'];
            // remove old instance definitions for 'Category'
            $instancetable = $xartable['security_instances'];
            $query = "DELETE FROM $instancetable
                      WHERE module_id= $sysid AND component='Category'";
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
            xarDefineInstance('categories', 'Category', $instances,1,$categorytable,'id',
            'parent_id','Instances of the categories module, including multilevel nesting');

            // fall through to the next upgrade

        case '2.3.1':

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
 * @author  Jim McDonald, Flávio Botelho <nuncanada@xaraya.com>, mikespub <postnuke@mikespub.net>
 * @access  public
 * @param   no parameters
 * @return  true on success or false on failure
 * @todo    restore the default behaviour prior to 1.0 release
*/
function categories_delete()
{
    return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => 'categories'));
}

?>
