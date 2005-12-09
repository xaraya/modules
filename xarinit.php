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

	$nextitemtype = xarModAPIFunc('dynamicdata','admin','getnextitemtype',array('modid' => 27));
	$new = array('name' => 'commerceroles',
				 'label' => 'CommerceRoles',
				 'moduleid' => 27,
				 'itemtype' => $nextitemtype,
				 'urlparam' => 'itemid',
				 'parent' => ROLES_GROUPTYPE,
				);
	$objectid = xarModAPIFunc('dynamicdata','admin','createobject',$new);
	$new = array('name' => 'suppliers',
				 'label' => 'Suppliers',
				 'moduleid' => 27,
				 'itemtype' => $nextitemtype+1,
				 'urlparam' => 'itemid',
				 'parent' => $nextitemtype,
				);
	$objectid = xarModAPIFunc('dynamicdata','admin','createobject',$new);
	$new = array('name' => 'manufacturers',
				 'label' => 'Manufacturers',
				 'moduleid' => 27,
				 'itemtype' => $nextitemtype+2,
				 'urlparam' => 'itemid',
				 'parent' => $nextitemtype+1,
				);
	$objectid = xarModAPIFunc('dynamicdata','admin','createobject',$new);

    xarMakeRoleMemberByName('commerceroles','Everybody');
    xarMakeRoleMemberByName('suppliers','commerceroles');
    xarMakeRoleMemberByName('manufacturers','commerceroles');
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

    return true;
}

?>
