<?php
/**
 * amazonfps
 *
 * @package modules
 * @copyright (C) 2009 WebCommunicate.net
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage amazonfps
 * @link http://xaraya.com/index.php/release/1033.html
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
function amazonfps_init()
{

    $dbconn =& xarDB::getConn();
    $tables =& xarDB::getTables();

    $prefix = xarDB::getPrefix();
    $tables['payments'] = $prefix . '_amazonfps_payments'; 

    // Create tables inside a transaction
    try {
        $charset = xarSystemVars::get(sys::CONFIG, 'DB.Charset');
        $dbconn->begin();

        $fields = array(
                        'itemid' => array('type' => 'integer', 'unsigned' => true, 'null' =>        false, 'increment' => true, 'primary_key' => true),
                        'refitemid' => array('type' => 'integer', 'unsigned' => true, 'null' => false),
                        'modulename' => array('type' => 'varchar','size' => 254,'null' =>        false, 'charset' => $charset),
                        'objectname' => array('type' => 'varchar','size' => 254,'null' =>        false, 'charset' => $charset),
                        'amount' =>  array('type' => 'integer', 'unsigned' => true, 'null' =>        false),
                        'currency' => array('type' => 'varchar','size' => 254,'null' => false,        'charset' => $charset),
                        'description' => array('type' => 'varchar','size' => 254,'null' =>        false, 'charset' => $charset),
                        'conversion' =>      array('type'        => 'boolean', 'default'     => false),
                        'success_url' => array('type' => 'varchar','size' => 254,'null' =>        false, 'charset' => $charset),
                        'transactionid' => array('type' => 'varchar','size' => 254,'null' =>        false, 'charset' => $charset),
                        'transactionstatus' => array('type' => 'varchar','size' => 254,'null' =>        false, 'charset' => $charset),
                        'requestid' => array('type' => 'varchar','size' => 254,'null' =>        false, 'charset' => $charset),
                        'paymentmethod' => array('type' => 'varchar','size' => 254,'null' =>        false, 'charset' => $charset)
            );
        $query = xarDBCreateTable($tables['payments'],$fields);
        $dbconn->Execute($query);

        $dbconn->commit();

    } catch (Exception $e) {
        $dbconn->rollback();
        throw $e;
    }

    $module = 'amazonfps';
    $objects = array(
                'amazonfps_payments',
                'amazonfps_module_settings' 
                );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;    

    xarModVars::set('amazonfps','public_key',''); 
    xarModVars::set('amazonfps','secret_key',''); 
    
    xarModVars::set('amazonfps','enable_filters',1);  
    xarModVars::set('amazonfps','filters_min_items',9);   
    xarModVars::set('amazonfps','callerreference_prefix','');  

    $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'amazonfps'));
    $module_settings->initialize();

# --------------------------------------------------------
# Create privilege instances
# 
 
    xarDefineInstance('amazonfps', 'Item', array()); 

# --------------------------------------------------------
#
# Register masks
#
    xarRegisterMask('ViewAmazonFPS','All','amazonfps', 'Item','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadAmazonFPS','All','amazonfps', 'Item','All','ACCESS_READ');
    xarRegisterMask('EditAmazonFPS','All','amazonfps', 'Item','All','ACCESS_EDIT');
    xarRegisterMask('AddAmazonFPS','All','amazonfps', 'Item','All','ACCESS_ADD');
    xarRegisterMask('DeleteAmazonFPS','All','amazonfps', 'Item','All','ACCESS_DELETE');
    xarRegisterMask('AdminAmazonFPS','All','amazonfps', 'Item','All','ACCESS_ADMIN');
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
function amazonfps_upgrade($oldversion)
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
function amazonfps_delete()
{
    // UnRegister blocks
    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName' => 'amazonfps',
                             'blockType' => 'first'))) return;

# --------------------------------------------------------
#
# Uninstall the module
#
# The function below pretty much takes care of everything that needs to be removed
#
    $module = 'amazonfps';
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $module));
}

?>