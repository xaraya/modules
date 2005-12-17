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

	$ice_objects = unserialize(xarModGetVar('commerce','ice_objects'));
	$objs = array_flip($ice_objects);
	$objects = $ice_objects;
	$nextitemtype = xarModAPIFunc('dynamicdata','admin','getnextitemtype',array('modid' => 27));
	$parent = xarModAPIFunc('dynamicdata','user','getobjectinfo',array('objectid' => $objs['ice_roles']));

	$new = array('name' => 'ice_suppliers',
				 'label' => 'Suppliers',
				 'moduleid' => 27,
				 'itemtype' => $nextitemtype,
				 'urlparam' => 'itemid',
				 'parent' => $parent['itemtype'],
				);
	$objectid = xarModAPIFunc('dynamicdata','admin','createobject',$new);
	$objects[$objectid] = 'ice_suppliers';

	$new = array('name' => 'ice_manufacturers',
				 'label' => 'Manufacturers',
				 'moduleid' => 27,
				 'itemtype' => $nextitemtype+1,
				 'urlparam' => 'itemid',
				 'parent' => $parent['itemtype'],
				);
	$objectid = xarModAPIFunc('dynamicdata','admin','createobject',$new);
	$objects[$objectid] = 'ice_manufacturers';

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
	$commerceobjects = array_flip(unserialize(xarModGetVar('commerce','ice_objects')));
	$result = xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $commerceobjects['ice_suppliers']));
	$result = xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $commerceobjects['ice_manufacturers']));

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
