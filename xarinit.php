<?php

function foo_init()
{

# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewFoo','All','foo','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('AdminFoo','All','foo','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('AdminFoo','All','foo','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up modvars
#
    xarModSetVar('foo', 'itemsperpage', 20);

# --------------------------------------------------------
#
# Set up hooks
#
    // This is a GUI hook for the roles module that enhances the roles profile page
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'foo', 'user', 'usermenu')) {
        return false;
    }

    xarModAPIFunc('modules', 'admin', 'enablehooks',
        array('callerModName' => 'foo', 'hookModName' => 'foo'));

    return true;
}

function foo_upgrade()
{
    return true;
}

function foo_delete()
{
    // Load table maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Generate the SQL to drop the table using the API
    $prefix = xarDBGetSiteTablePrefix();
    $table = $prefix . "_foo";
    $query = xarDBDropTable($table);
    if (empty($query)) return; // throw back

    xarRemoveMasks('foo');
    xarRemoveInstances('foo');
    xarModDelAllVars('foo');

    return true;
}

?>
