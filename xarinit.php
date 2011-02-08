<?php
/**
 * Content
 *
 * @package modules
 * @copyright (C) 2009 WebCommunicate.net
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage content
 * @link http://www.xaraya.com/index.php/release/1015.html
 * @author Ryan Walker <ryan@webcommunicate.net>
 */
sys::import('xaraya.tableddl'); 
/**
 * Initialise the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool True on succes of init
 */
function content_init()
{

    $dbconn =& xarDB::getConn();
    $tables =& xarDB::getTables();

    $prefix = xarDB::getPrefix();
    $tables['content'] = $prefix . '_content';
    $tables['content_types'] = $prefix . '_content_types';

    // Create tables inside a transaction
    try {
        $charset = xarSystemVars::get(sys::CONFIG, 'DB.Charset');
        $dbconn->begin();

        $fields = array(
                        'itemid' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
                        'content_type' => array('type' => 'varchar','size' => 254,'null' => false, 'charset' => $charset),
                        'item_path' => array('type' => 'varchar','size' => 254,'null' => false, 'charset' => $charset)
			);
        $query = xarDBCreateTable($tables['content'],$fields);
        $dbconn->Execute($query);

		$fields = array(
                        'itemid' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
                        'content_type' => array('type' => 'varchar','size' => 254,'null' => false, 'charset' => $charset),
						'label' => array('type' => 'varchar','size' => 254,'null' => false, 'charset' => $charset),
						'model' => array('type' => 'varchar','size' => 254,'null' => false, 'charset' => $charset)
			);
        $query = xarDBCreateTable($tables['content_types'],$fields);
        $dbconn->Execute($query);

		$index = array('name' => $prefix . '_content_content_types',
                       'fields' => array('content_type'),
                       'unique' => true
                       );
        $query = xarDBCreateIndex($tables['content_types'], $index);
        $dbconn->Execute($query);

        // We're done, commit
        $dbconn->commit();
    } catch (Exception $e) {
        $dbconn->rollback();
        throw $e;
    }
	PropertyRegistration::importPropertyTypes(false,array('modules/content/xarproperties'));

    $module = 'content';
    $objects = array(
                'content',
				'content_types',
				'content_module_settings',
				'content_user_settings' 
                );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;    

	// create an 'apples' content type for testing and demo
	$def_file = sys::code() . 'modules/content/xardata/apples-def.xml';
	if(!xarMod::apiFunc('content','util','import', array('file' => $def_file))) return; 
	
	// use the dd import function for the items
	$dat_file = sys::code() . 'modules/content/xardata/apples-dat.xml';
	xarMod::apiFunc('dynamicdata','util','import', array('file' => $dat_file));

# --------------------------------------------------------
#
# Set up configuration modvars (module specific)
#
	xarModVars::set('content','default_ctype','');
	xarModVars::set('content','default_itemid',1);
	xarModVars::set('content','default_main_page_tpl','default');
	xarModVars::set('content','default_display_page_tpl','default');
	xarModVars::set('content','default_view_page_tpl','default');
	xarModVars::set('content','enable_filters',1);  
	xarModVars::set('content','filters_min_ct_count',9);    
	xarModVars::set('content','filters_min_item_count',1);

# --------------------------------------------------------
#
# Set up configuration modvars (general)
#

        $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'content'));
        $module_settings->initialize();

# --------------------------------------------------------
#
# Register blocks
#
/*    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName' => 'content',
                             'blockType' => 'first'))) return;*/
# --------------------------------------------------------
#
# Create privilege instances
#

    $instances = array(
						array(
							'header' => 'Itemid:',
							'query' => "SELECT itemid FROM " . $prefix . "_content",
							'limit' => 20
						),
						array(
							'header' => 'Content Type:',
							'query' => "SELECT DISTINCT content_type FROM " . $prefix . "_content_types",
							'limit' => 20
						),
						array(
							'header' => 'Author ID:',
							'query' => "SELECT id FROM " . $prefix . "_roles",
							'limit' => 20
						)
                    );
    xarDefineInstance('content', 'Item', $instances);

	$instances = array(
								array(
							'header' => 'Content Type:',
							'query' => "SELECT DISTINCT content_type FROM " . $prefix . "_content_types",
							'limit' => 20
						)
					);
    xarDefineInstance('content', 'ContentType', $instances);

# --------------------------------------------------------
#
# Register masks
#
    //And standard masks for the rest - keep names the same as any prior so minimal sec checks in templates still work
    xarRegisterMask('ViewContent',    'All', 'content', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadContent',    'All', 'content', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditContent',    'All', 'content', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddContent',     'All', 'content', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteContent',  'All', 'content', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminContent',   'All', 'content', 'Item', 'All:All:All', 'ACCESS_ADMIN');

	xarRegisterMask('ViewContentTypes',    'All', 'content', 'ContentType', 'All', 'ACCESS_OVERVIEW');
	xarRegisterMask('ReadContentTypes',    'All', 'content', 'ContentType', 'All', 'ACCESS_READ');
    xarRegisterMask('EditContentTypes',    'All', 'content', 'ContentType', 'All', 'ACCESS_EDIT');
    xarRegisterMask('AddContentTypes',     'All', 'content', 'ContentType', 'All', 'ACCESS_ADD');
    xarRegisterMask('DeleteContentTypes',  'All', 'content', 'ContentType', 'All', 'ACCESS_DELETE');
    xarRegisterMask('AdminContentTypes',   'All', 'content', 'ContentType', 'All', 'ACCESS_ADMIN');
# --------------------------------------------------------
#
# Register hooks
#

    // Initialisation successful
    return true;
}

/**
 * Upgrade the module from an old version
 */
function content_upgrade($oldversion)
{
	$old = str_replace('.','',$oldversion);
	$old = (int)$old;

    if ($old < 70) {
		xarMod::apiFunc('content','util','upgradepre070');
    } 
	if ($old < 90) {
		xarMod::apiFunc('content','util','upgradepre090');
	}
	if ($old < 91) {
		PropertyRegistration::importPropertyTypes(false,array('modules/content/xarproperties'));
	}

    // Update successful
    return true;
}

/**
 * Delete the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool true on success of deletion
 */
function content_delete()
{
    // UnRegister blocks
   /* if (!xarMod::apiFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName' => 'content',
                             'blockType' => 'first'))) return;*/

	// Delete the objects for the content_types
	$content_types = xarMod::apiFunc('content','admin','getcontenttypes');

	sys::import('modules.dynamicdata.class.objects.master');

	foreach ($content_types as $key=>$value) {
		$list = DataObjectMaster::getObjectList(array('name' => 'objects'));
		$filters = array(
			'where' => 'name eq \'' . $key . '\''
		);
		$items = $list->getItems($filters);
		$item = reset($items);
		$objectid = $item['objectid'];
		$object = DataObjectMaster::getObject(array('name' => 'objects'));
		$object->deleteItem(array('itemid' => $objectid));
	}
 
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => 'content'));
}

?>