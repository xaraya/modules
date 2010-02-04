<?php
/**
 * Sharecontent Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage sharecontent Module
 * @link http://xaraya.com/index.php/release/894.html
 * @author Andrea Moro
 */
/**
 * initialise the sharecontent module
 */
function sharecontent_init()
{
    $module = 'sharecontent';
# --------------------------------------------------------
#
# Create tables
#
    $dbconn =& xarDB::getConn();
    $tables =& xarDB::getTables();
    $prefix = xarDB::getPrefix();
    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    $sctable = $tables['sharecontent'];

    // Create tables inside a transaction
    try {
        $charset = xarSystemVars::get(sys::CONFIG, 'DB.Charset');
        $dbconn->begin();
        sys::import('xaraya.structures.query');
        $q = new Query();
        // sharecontent table
        $query = "DROP TABLE IF EXISTS " . $sctable;
        if (!$q->run($query)) return;
        $fields = array(
            'id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
            'title' => array('type' => 'varchar','size' => 64,'null' => false, 'charset' => $charset),
            'homeurl' => array('type' => 'varchar','size' => 128,'null' => false, 'charset' => $charset),
            'submiturl' => array('type' => 'varchar','size' => 128,'null' => false, 'charset' => $charset),
            'image' => array('type' => 'varchar','size' => 128,'null' => false, 'charset' => $charset),
            'active' => array('type' => 'boolean','null' => false, 'default' => 1)
        );
        $query = xarDBCreateTable($sctable,$fields);
        $dbconn->Execute($query);
        // We're done, commit
        $dbconn->commit();
    } catch (Exception $e) {
        $dbconn->rollback();
        throw $e;
    }

# --------------------------------------------------------
#
# Set up websites
#
    // Load the initial setup of the websites
    $file = sys::code() . 'modules/sharecontent/xarsetup.php';
    if (file_exists($file)) {
        include $file;
    } else {
        $websites = array();
    }

    // Save  websites
    foreach ($websites as $website) {
        list($title,$homeurl,$submiturl,$image,$active) = $website;
        $nextId = $dbconn->GenId($sctable);
        $query = "INSERT INTO $sctable (id, title, homeurl, submiturl, image, active) VALUES (?,?,?,?,?,?)";
        $bindvars = array($nextId,$title,$homeurl,$submiturl,$image,$active);
        $result =& $dbconn->Execute($query,$bindvars);
        if (!$result)  sharecontent_delete();
    }

# --------------------------------------------------------
#
# Set up configuration modvars (module specific)
#
    xarModVars::set('sharecontent', 'enablemail', '0');
    xarModVars::set('sharecontent', 'maxemails', '1');
    xarModVars::set('sharecontent', 'htmlmail', '0');
    xarModVars::set('sharecontent', 'bcc', '');

# --------------------------------------------------------
#
# Set up configuration modvars (common)
#
    $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => $module));
    $module_settings->initialize();

# --------------------------------------------------------
#
# Register module hooks
#
    // Display item
    if (!xarModRegisterHook('item', 'display', 'GUI', $module, 'user', 'display'))
        return false;

# --------------------------------------------------------
#
# Create privilege instances
#

	$query = "SELECT DISTINCT xar_smodule FROM $tables[hooks] WHERE xar_tmodule='sharecontent'  ";
	$instances = array( array('header'=>'Hooked module','query'=>$query,'limit'=>20));
    xarDefineInstance('sharecontent', 'Web', $instances);
    xarDefineInstance('sharecontent', 'Mail', $instances);

    // Register the module components that are privileges objects
    // Format: xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    xarRegisterMask('ReadSharecontentWeb', 'All', 'sharecontent', 'Web', 'All', 'ACCESS_READ');
    xarRegisterMask('SendSharecontentMail', 'All', 'sharecontent', 'Mail', 'All', 'ACCESS_COMMENT');
    xarRegisterMask('AdminSharecontent', 'All', 'sharecontent', 'All', 'All', 'ACCESS_ADMIN');

    // Initialisation successful
	return true;
}

/**
 * upgrade the sharecontent module from an old version
 * @param string oldversion
 * @return bool true on success of upgrade
 */
function sharecontent_upgrade($oldversion)
{
    $dbconn =& xarDB::getConn();
    $tables =& xarDB::getTables();
    $prefix = xarDB::getPrefix();
    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    $sctable = $tables['sharecontent'];

    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '2.0.0':

        break;
    }

    return true;
}

/**
 * delete the sharecontent module
 * @return bool true on successful deletion
 */
function sharecontent_delete()
{
    $module = 'sharecontent';

    $dbconn =& xarDB::getConn();
    $tables =& xarDB::getTables();
    $prefix = xarDB::getPrefix();
    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    $sctable = $tables['sharecontent'];

    if (!xarModUnregisterHook('item', 'display', 'GUI',
                              $module, 'user', 'display')) {
        return false;
    }
    /* @FIXME: this hook doesn't exist. Undocumented todo?
    if (!xarModUnregisterHook('module', 'remove', 'API',
                             'sharecontent', 'admin', 'deleteall')) {
        return;
    }
    */

# --------------------------------------------------------
#
# Uninstall the module
#
# The function below pretty much takes care of everything else that needs to be removed
#
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $module));

    /* Deletion successful*/
    return true;
}

?>
