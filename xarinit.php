<?php
/**
 *
 * Initialise the foo module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Foo Module
 * @author Marc Lutolf <mfl@netspan.ch>
 * *
 * @param to be added
 * @return to be added
 *
 */

function foo_init()
{

# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewFoo','All','foo','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadFoo','All','foo','All','All','ACCESS_READ');
    xarRegisterMask('AdminFoo','All','foo','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('AdminFoo','All','foo','All','All','ACCESS_ADMIN');
    xarMakePrivilegeRoot('AdminFoo');

# --------------------------------------------------------
#
# Set up modvars
#
    xarModVars::set('foo', 'itemsperpage', 20);
    xarModVars::set('foo', 'useModuleAlias',0);
    xarModVars::set('foo', 'aliasname','Foo');

    // Add variables like this next one when creating utility modules
    // This variable is referenced in the xaradmin/modifyconfig-utility.php file
    // This variable is referenced in the xartemplates/includes/defaults.xd file
//    xarModVars::set('foo', 'bar', 'Bar');

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
    // Only change the next line. No need for anything else
    $this_module = 'foo';

# --------------------------------------------------------
#
# Remove database tables
#
    // Load table maintenance API
    sys::import('xaraya.tableddl');

    // Generate the SQL to drop the table using the API
    $prefix = xarDB::getPrefix();
    $table = $prefix . "_" . $this_module;
    $query = xarDBDropTable($table);
    if (empty($query)) return; // throw back

# --------------------------------------------------------
#
# Delete all DD objects created by this module
#
    try {
        $dd_objects = unserialize(xarModVars::get($this_module,$this_module . '_objects'));
        foreach ($dd_objects as $key => $value)
            $result = xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $value));
    } catch (Exception $e) {}

# --------------------------------------------------------
#
# Remove the categories
#
    try {
        xarModAPIFunc('categories', 'admin', 'deletecat',
                             array('cid' => xarModVars::get($this_module, 'basecategory'))
                            );
    } catch (Exception $e) {}

# --------------------------------------------------------
#
# Remove modvars, masks and privilege instances
#
    xarRemoveMasks($this_module);
    xarRemoveInstances($this_module);
    xarModDelAllVars($this_module);

    return true;
}

?>
