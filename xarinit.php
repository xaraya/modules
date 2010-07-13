<?php
/**
 * Downloads
 *
 * @package modules
 * @copyright (C) 2009 WebCommunicate.net
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage downloads
 * @link http://www.xaraya.com/index.php/release/19741.html
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
function downloads_init()
{

    $dbconn =& xarDB::getConn();
    $tables =& xarDB::getTables();

    $prefix = xarDB::getPrefix();
    $tables['downloads'] = $prefix . '_downloads';

    // Create tables inside a transaction
    try {
        $charset = xarSystemVars::get(sys::CONFIG, 'DB.Charset');
        $dbconn->begin();

        $fields = array(
                        'itemid' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
                        'title' => array('type' => 'varchar','size' => 254,'null' => false, 'charset' => $charset),
						'ext' => array('type' => 'varchar','size' => 4,'null' => false, 'charset' => $charset),
						'description' => array('type' => 'text', 'null' => false, 'charset' => $charset),
			'status' => array('type' => 'varchar', 'size' => 1, 'null' => false, 'charset' => $charset),
						'filename' => array('type' => 'varchar','size' => 254,'null' => false, 'charset' => $charset),
						'location' => array('type' => 'varchar','size' => 254, 'null' => false, 'charset' => $charset)
			);
        $query = xarDBCreateTable($tables['downloads'],$fields);
        $dbconn->Execute($query);

        // We're done, commit
        $dbconn->commit();
    } catch (Exception $e) {
        $dbconn->rollback();
        throw $e;
    }

    $module = 'downloads';
    $objects = array(
                'downloads',
				'downloads_module_settings',
				'downloads_user_settings'
                );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;    

# --------------------------------------------------------
#
# Set up configuration modvars (module specific)
#
	xarModVars::set('downloads','file_directories','../downloads_module_files');
	xarModVars::set('downloads','file_extensions','gif, jpg, jpeg, png, pdf, doc, txt');
	xarModVars::set('downloads','maximum_filesize','1000000');
	xarModVars::set('downloads','enable_filters',true);
	xarModVars::set('downloads','show_xarmodurl','1');
	xarModVars::set('downloads','filters_records_min_item_count','1');
	xarModVars::set('downloads','filters_files_min_item_count','1');	

# --------------------------------------------------------
#
# Set up configuration modvars (general)
#

        $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'downloads'));
        $module_settings->initialize();

# --------------------------------------------------------
#
# Register blocks
#
/*    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName' => 'downloads',
                             'blockType' => 'first'))) return;*/
# --------------------------------------------------------
#
# Create privilege instances
#
    //$object = DataObjectMaster::getObject(array('name' => 'downloads'));
    //$objectid = $object->objectid;

    // Note : we could add some other fields in here too, based on the properties we imported above
    $instances = array(
						array(
							'header' => 'Itemid:',
							'query' => "SELECT itemid FROM " . $prefix . "_downloads",
							'limit' => 20
						),
						array(
							'header' => 'File Type:',
							'query' => "SELECT DISTINCT ext FROM " . $prefix . "_downloads",
							'limit' => 20
						),
						array(
							'header' => 'Contributor:',
							'query' => "SELECT DISTINCT uid FROM " . $prefix . "_roles",
							'limit' => 20
						)
                    );
    xarDefineInstance('downloads', 'Item', $instances);

# --------------------------------------------------------
#
# Register masks
#
    //And standard masks for the rest - keep names the same as any prior so minimal sec checks in templates still work
    xarRegisterMask('ViewDownloads',    'All', 'downloads', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadDownloads',    'All', 'downloads', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditDownloads',    'All', 'downloads', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddDownloads',     'All', 'downloads', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteDownloads',  'All', 'downloads', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminDownloads',   'All', 'downloads', 'Item', 'All:All:All', 'ACCESS_ADMIN');

# --------------------------------------------------------
#
# Register hooks
#

    // Initialisation successful
    return true;
}

/**
 * Upgrade the module from an old version
 *
 * This function can be called multiple times
 */
function downloads_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '2.0.0':
            // Code to upgrade from version 2.0 goes here
            break;
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
function downloads_delete()
{
    // UnRegister blocks
   /* if (!xarMod::apiFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName' => 'downloads',
                             'blockType' => 'first'))) return;*/
 
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => 'downloads'));
}

?>