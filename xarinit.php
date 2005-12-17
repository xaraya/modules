<?php

function vendors_init()
{

# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewVendors','All','vendors','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('AdminVendors','All','vendors','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('AdminVendors','All','vendors','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up modvars
#
    xarModSetVar('vendors', 'itemsperpage', 20);

# --------------------------------------------------------
#
# Set up hooks
#
    // This is a GUI hook for the roles module that enhances the roles profile page
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'vendors', 'user', 'usermenu')) {
        return false;
    }

    xarModAPIFunc('modules', 'admin', 'enablehooks',
        array('callerModName' => 'roles', 'hookModName' => 'vendors'));

# --------------------------------------------------------
#
# Set extensions
#

    $ice_objects = array('ice_suppliers');

    // Treat destructive right now
    $existing_objects  = xarModApiFunc('dynamicdata','user','getobjects');
    foreach($existing_objects as $objectid => $objectinfo) {
        if(in_array($objectinfo['name'], $ice_objects)) {
            // KILL
            if(!xarModApiFunc('dynamicdata','admin','deleteobject', array('objectid' => $objectid))) return;
        }
    }

	$objects = unserialize(xarModGetVar('commerce','ice_objects'));
    foreach($ice_objects as $ice_object) {
        $def_file = 'modules/vendors/xardata/'.$ice_object.'-def.xml';
        $dat_file = 'modules/vendors/xardata/'.$ice_object.'-data.xml';

        $objectid = xarModAPIFunc('dynamicdata','util','import', array('file' => $def_file));
        if (!$objectid) continue;
        else $objects[$ice_object] = $objectid;
        // Let data import be allowed to be empty
        if(file_exists($dat_file)) {
            // And allow it to fail for now
            xarModAPIFunc('dynamicdata','util','import', array('file' => $dat_file,'keepitemid' => true));
        }
    }

	xarModSetVar('commerce','ice_objects',serialize($objects));

	$parent = xarFindRole('CommerceRoles');
	$new = array('name' => 'Suppliers',
				 'itemtype' => ROLES_GROUPTYPE,
				 'parentid' => $parent->getID(),
				);
	$uid1 = xarModAPIFunc('roles','admin','create',$new);
	$new = array('name' => 'Manufacturers',
				 'itemtype' => ROLES_GROUPTYPE,
				 'parentid' => $parent->getID(),
				);
	$uid1 = xarModAPIFunc('roles','admin','create',$new);

    return true;
}

function vendors_upgrade()
{
    return true;
}

function vendors_delete()
{
    // Load table maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Generate the SQL to drop the table using the API
    $prefix = xarDBGetSiteTablePrefix();
    $table = $prefix . "_vendors";
    $query = xarDBDropTable($table);
    if (empty($query)) return; // throw back

    // Delete the DD objects created by this module
	$ice_objects = unserialize(xarModGetVar('commerce','ice_objects'));
	if (isset($ice_objects['ice_suppliers']))
		$result = xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $ice_objects['ice_suppliers']));
	if (isset($ice_objects['ice_manufacturers']))
		$result = xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $ice_objects['ice_manufacturers']));

	// Purge all the roles created by this module
	$role = xarFindRole('Suppliers');
	$descendants = $role->getDescendants();
	foreach ($descendants as $item)
		if (!$item->purge()) return;
	if (!$role->purge()) return;

	$role = xarFindRole('Manufacturers');
	$descendants = $role->getDescendants();
	foreach ($descendants as $item)
		if (!$item->purge()) return;
	if (!$role->purge()) return;

    // Remove Masks and Instances
    xarRemoveMasks('vendors');
    xarRemoveInstances('vendors');

    // Remove Modvars
    xarModDelAllVars('vendors');

    return true;
}

?>
