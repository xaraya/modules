<?php
/**
 * menutree
 *
 * @package modules
 * @copyright (C) 2009 WebCommunicate.net
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage menutree
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
function menutree_init()
{

    $dbconn =& xarDB::getConn();
    $tables =& xarDB::getTables();

    $prefix = xarDB::getPrefix();
    $tables['menutree'] = $prefix . '_menutree'; 

    // Create tables inside a transaction
    try {
        $charset = xarSystemVars::get(sys::CONFIG, 'DB.Charset');
        $dbconn->begin(); 

        $fields = array(
                        'itemid' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
                        'link' => array('type' => 'varchar','size' => 255,'null' => false, 'charset' => $charset),
						'seq' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => false), 
						'parentid' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => false), 
						'no_rename' => array('type' => 'boolean',
						 'default'     => false), 
						'no_delete' => array('type' => 'boolean',
						'default'     => false), 
			);
        $query = xarDBCreateTable($tables['menutree'],$fields);
        $dbconn->Execute($query);


        // We're done, commit
        $dbconn->commit();
    } catch (Exception $e) {
        $dbconn->rollback();
        throw $e;
    }

    $module = 'menutree';
    $objects = array(
                'menutree',
				'menutree_module_settings',
				'menutree_user_settings'
                );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;    

# --------------------------------------------------------
#
# Set up configuration modvars (module specific)
#
	//xarModVars::set('menutree','file_directories','menutree_module_files');

# --------------------------------------------------------
#
# Set up configuration modvars (general)
#

        $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'menutree'));
        $module_settings->initialize();

# --------------------------------------------------------
#
# Register blocks
#
/*    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName' => 'menutree',
                             'blockType' => 'first'))) return;*/
# --------------------------------------------------------
#
# Create privilege instances
#
    //$object = DataObjectMaster::getObject(array('name' => 'menutree'));
    //$objectid = $object->objectid;

    // Note : we could add some other fields in here too, based on the properties we imported above
    

# --------------------------------------------------------
#
# Register masks
#
    //And standard masks for the rest - keep names the same as any prior so minimal sec checks in templates still work
    xarRegisterMask('ViewMenuTree',    'All', 'menutree', 'Item', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadMenuTree',    'All', 'menutree', 'Item', 'All', 'ACCESS_READ');
	xarRegisterMask('SubmitMenuTree',    'All', 'menutree', 'Item', 'All', 'ACCESS_COMMENT');
    xarRegisterMask('EditMenuTree',    'All', 'menutree', 'Item', 'All', 'ACCESS_EDIT');
    xarRegisterMask('AddMenuTree',     'All', 'menutree', 'Item', 'All', 'ACCESS_ADD');
    xarRegisterMask('DeleteMenuTree',  'All', 'menutree', 'Item', 'All', 'ACCESS_DELETE');
    xarRegisterMask('AdminMenuTree',   'All', 'menutree', 'Item', 'All', 'ACCESS_ADMIN');

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
function menutree_upgrade($oldversion)
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
function menutree_delete()
{
    // UnRegister blocks
   /* if (!xarMod::apiFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName' => 'menutree',
                             'blockType' => 'first'))) return;*/
 
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => 'menutree'));
}

?>