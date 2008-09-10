<?php
/**
 *
 * Initialise or remove the foo module
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
        xarRegisterMask('CommentFoo','All','foo','All','All','ACCESS_COMMENT');
        xarRegisterMask('ModerateFoo','All','foo','All','All','ACCESS_MODERATE');
        xarRegisterMask('EditFoo','All','foo','All','All','ACCESS_EDIT');
        xarRegisterMask('AddFoo','All','foo','All','All','ACCESS_ADD');
        xarRegisterMask('ManageFoo','All','foo','All','All','ACCESS_DELETE');
        xarRegisterMask('AdminFoo','All','foo','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up privileges
    #
        xarRegisterPrivilege('ViewFoo','All','foo','All','All','ACCESS_OVERVIEW');
        xarRegisterPrivilege('ReadFoo','All','foo','All','All','ACCESS_READ');
        xarRegisterPrivilege('CommentFoo','All','foo','All','All','ACCESS_COMMENT');
        xarRegisterPrivilege('ModerateFoo','All','foo','All','All','ACCESS_MODERATE');
        xarRegisterPrivilege('EditFoo','All','foo','All','All','ACCESS_EDIT');
        xarRegisterPrivilege('AddFoo','All','foo','All','All','ACCESS_ADD');
        xarRegisterPrivilege('ManageFoo','All','foo','All','All','ACCESS_DELETE');
        xarRegisterPrivilege('AdminFoo','All','foo','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up modvars
    #
        xarModVars::set('foo', 'itemsperpage', 20);
        xarModVars::set('foo', 'useModuleAlias',0);
        xarModVars::set('foo', 'aliasname','Foo');
        xarModVars::set('foo', 'currentmastertable','foo_foo');

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
        xarModVars::delete_all($this_module);

        return true;
    }

?>
