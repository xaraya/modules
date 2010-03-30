<?php
/**
 * shop
 *
 * @package modules
 * @copyright (C) 2009 WebCommunicate.net
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage shop
 * @link http://xaraya.com/index.php/release/1031.html
 * @author Ryan Walker <ryan@webcommunicate.net>
 */
/**
 * Initialise the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool True on succes of init
 */
function shop_init()
{
# --------------------------------------------------------
#
# Create DD objects
#
# The object XML files located in the xardata folder of the module.
# The file names have the form e.g.
#     shop-def.xml
#     shop-dat.xml
#
# The first is a definition file for the object, and needs to be present if you list shop
# among the objects to be created in the array below.
#
# The second is a defintion file for the object's items, i.e. its data. This file can be omitted.
#
# You can create these files manually, for example by cutting and pasting from an existing example.
# The easier way is to create an object (and perhaps its items) using the user interface of the
# DynamicData module. Once you have an object (and items), you can export it into an XML file using the
# DD module's export facility.
#
# Note: the object(s) created below are automatically kept track of so that the module knows to remove them when
# you deinstall it.
#
    $module = 'shop';
    $objects = array(
                'shop_products',
				'shop_transactions',
				'shop_attributes',
				'shop_customers',
				'shop_paymentmethods',
				'shop_module_settings',
				'shop_user_settings'
                );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;    
# --------------------------------------------------------
#
# Set up configuration modvars (module specific)
#
# Since this modvar is used as storage in a DD object shop_module_settings,
# we could also let Xaraya define it, but that would mean we wouldn't have it until
# we updated the modifyconfig page
#
    xarModVars::set('shop','payment_gateway',1);
	xarModVars::set('shop','pg_id','');
	xarModVars::set('shop','pg_key','');
	xarModVars::set('shop','pg_api_signature','');
	xarModVars::set('shop','pg_notes','A place to store test credit card numbers etc');

# --------------------------------------------------------
#
# Set up configuration modvars (general)
#
# The common settings use the module_settings dataobject. which is created when Xaraya is installed
# These next lines initialize the appropriate modvars that object uses for shop, if they don't already exist.
# The lines below corresponding to the initializeation of the core modules are found in modules/installer/xaradmin.php.
# The module_settings dataobject itself is defined in the dynamicdata module.
#
        $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'shop'));
        $module_settings->initialize();

# --------------------------------------------------------
#
# Register blocks
#
/*    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName' => 'shop',
                             'blockType' => 'first'))) return;*/
# --------------------------------------------------------
#
# Create privilege instances
#
    sys::import('modules.dynamicdata.class.objects.master');
    $object = DataObjectMaster::getObject(array('name' => 'shop_transactions'));
    $objectid = $object->objectid;

    $xartable =& xarDB::getTables();
    $dynproptable = $xartable['dynamic_properties'];
    $dyndatatable = $xartable['dynamic_data'];
	$query = "SELECT DISTINCT $dynproptable.id
	FROM $dynproptable
	LEFT JOIN $dyndatatable
	ON $dyndatatable.id=property_id
	WHERE object_id= $objectid";

    // Note : we could add some other fields in here too, based on the properties we imported above
    $instances = array(
                        array('header' => 'shop ID:',
                                'query' => $query,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('shop', 'Item', $instances);

	xarRemoveMasks('shop');

# --------------------------------------------------------
#
# Register masks
#
    xarRegisterMask('ViewShop','All','shop','Item','All:All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadShop','All','shop','Item','All:All:All','ACCESS_READ');
    xarRegisterMask('EditShop','All','shop','Item','All:All:All','ACCESS_EDIT');
    xarRegisterMask('AddShop','All','shop','Item','All:All:All','ACCESS_ADD');
    xarRegisterMask('DeleteShop','All','shop','Item','All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminShop','All','shop','Item','All:All:All','ACCESS_ADMIN');
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
function shop_upgrade($oldversion)
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
function shop_delete()
{
    // UnRegister blocks
    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName' => 'shop',
                             'blockType' => 'first'))) return;

# --------------------------------------------------------
#
# Uninstall the module
#
# The function below pretty much takes care of everything that needs to be removed
#
    $module = 'shop';
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $module));
}

?>