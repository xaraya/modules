<?php
/**
 *
 * Initialise or remove the foo module
 *
 */

    sys::import('modules.query.class.query');

    function foo_init()
    {

    # --------------------------------------------------------
    #
    # Set tables
    #
        $q = new Query();
        $prefix = xarDB::getPrefix();
        
        $query = "DROP TABLE IF EXISTS " . $prefix . "_foo_tags";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_foo_tags (
            id                integer unsigned NOT NULL auto_increment,
            name              varchar(255) NOT NULL default '', 
            timecreated       int(11) unsigned NOT NULL default '0', 
            timelasthit       int(11) unsigned NOT NULL default '0', 
            state             tinyint(4) NOT NULL default '1', 
            role_id           int(11) unsigned NOT NULL default '0', 
            count             int(11) unsigned NOT NULL default '0', 
            PRIMARY KEY  (id), 
            KEY i_tag_name (name), 
            KEY i_tag_timecreated (timecreated), 
            KEY i_tag_ltimelasthit (timelasthit), 
            KEY i_tag_state (state), 
            KEY i_tag_role_id (role_id), 
            KEY i_tag_count (count) 
        ) TYPE=MyISAM";
        if (!$q->run($query)) return;
  
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
        xarModVars::set('foo', 'defaultmastertable','foo_foo');

        // Add variables like this next one when creating utility modules
        // This variable is referenced in the xaradmin/modifyconfig-utility.php file
        // This variable is referenced in the xartemplates/includes/defaults.xd file
    //    xarModVars::set('foo', 'bar', 'Bar');

    # --------------------------------------------------------
    #
    # Create DD objects
    #
        $module = 'foo';
        $objects = array(
                         );

        if(!xarModAPIFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

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
        $this_module = 'foo';
        return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => $this_module));
    }

?>
