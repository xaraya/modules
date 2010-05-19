<?php
/**
 * uspsws
 *
 * @package modules
 * @copyright (C) 2009 WebCommunicate.net
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage uspsws
 * @link http://xaraya.com/index.php/release/1033.html
 * @author Ryan Walker <ryan@webcommunicate.net>
 */
/**
 * Initialise the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool True on succes of init
 */
function uspsws_init()
{

    $module = 'uspsws';
    $objects = array(
                'uspsws_rate',
				'uspsws_module_settings',
				'uspsws_user_settings'
                );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;    

    xarModVars::set('uspsws','userid','');
	xarModVars::set('uspsws','password',''); 

	$module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'uspsws'));
	$module_settings->initialize();

# --------------------------------------------------------
# Create privilege instances
#
    sys::import('modules.dynamicdata.class.objects.master');
    $object = DataObjectMaster::getObject(array('name' => 'uspsws_rate'));
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
                        array('header' => 'uspsws ID:',
                                'query' => $query,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('uspsws', 'Item', $instances);

	xarRemoveMasks('uspsws');

# --------------------------------------------------------
#
# Register masks
#
    xarRegisterMask('ViewUSPSWS','All','uspsws','Item','All:All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadUSPSWS','All','uspsws','Item','All:All:All','ACCESS_READ');
    xarRegisterMask('EditUSPSWS','All','uspsws','Item','All:All:All','ACCESS_EDIT');
    xarRegisterMask('AddUSPSWS','All','uspsws','Item','All:All:All','ACCESS_ADD');
    xarRegisterMask('DeleteUSPSWS','All','uspsws','Item','All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminUSPSWS','All','uspsws','Item','All:All:All','ACCESS_ADMIN');
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
function uspsws_upgrade($oldversion)
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
function uspsws_delete()
{
    // UnRegister blocks
    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName' => 'uspsws',
                             'blockType' => 'first'))) return;

# --------------------------------------------------------
#
# Uninstall the module
#
# The function below pretty much takes care of everything that needs to be removed
#
    $module = 'uspsws';
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $module));
}

?>