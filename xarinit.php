<?php
/**
 * Foo Module
 *
 * @package modules
 * @subpackage foo module
 * @copyright (C) 2011 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 *
 * Initialise or remove the foo module
 *
 */

    sys::import('xaraya.structures.query');

    function foo_init()
    {

    # --------------------------------------------------------
    #
    # Set tables
    #
        $q = new Query();
        $prefix = xarDB::getPrefix();
        
        $query = "DROP TABLE IF EXISTS " . $prefix . "_foo_entries";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_foo_entries (
            id                integer unsigned NOT NULL auto_increment,
            name              varchar(254) NOT NULL default '', 
            timecreated       integer unsigned NOT NULL default 0, 
            role_id           integer unsigned NOT NULL default 0, 
            state             tinyint(3) NOT NULL default 3, 
            PRIMARY KEY  (id), 
            KEY i_tag_name (name)
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
    # Create DD objects
    #
        $module = 'foo';
        $objects = array(
                         );

        if(!xarModAPIFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

    # --------------------------------------------------------
    #
    # Set up modvars
    #
        $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'foo'));
        $module_settings->initialize();

        // Add variables like this next one when creating utility modules
        // This variable is referenced in the xaradmin/modifyconfig-utility.php file
        // This variable is referenced in the xartemplates/includes/defaults.xd file
        xarModVars::set('foo', 'defaultmastertable','foo_foo');

    # --------------------------------------------------------
    #
    # Set up hooks
    #

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
