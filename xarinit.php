<?php
/**
 * EAV Module
 *
 * @package modules
 * @subpackage eav
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2013 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 *
 * Initialise or remove the eav module
 *
 */

    sys::import('xaraya.structures.query');

    function eav_init()
    {

    # --------------------------------------------------------
    #
    # Set tables
    #
        $q = new Query();
        $prefix = xarDB::getPrefix();
        
        $query = "DROP TABLE IF EXISTS " . $prefix . "_eav_attributes";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_eav_attributes (
            id                integer unsigned NOT NULL auto_increment,
            object_id         integer unsigned NOT NULL default 0, 
            module_id         integer unsigned NOT NULL default 0, 
            name              varchar(254) NOT NULL default '', 
            label             varchar(254) NOT NULL default '', 
            property_id       integer unsigned NOT NULL default 0, 
            configuration     text, 
            default_tinyint   tinyint unsigned NOT NULL default 0, 
            default_integer   integer unsigned NOT NULL default 0, 
            default_decimal   decimal(15,5) default NULL,
            default_string    varchar(254) NOT NULL default '', 
            default_text      text, 
            timecreated       integer unsigned NOT NULL default 0, 
            timeupdated       integer unsigned NOT NULL default 0, 
            state             tinyint(3) NOT NULL default 3, 
            PRIMARY KEY  (id), 
            KEY i_tag_name (name)
        )";
        if (!$q->run($query)) return;
  
        $query = "DROP TABLE IF EXISTS " . $prefix . "_eav_data";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_eav_data (
            id                integer unsigned NOT NULL auto_increment,
            item_id           integer unsigned NOT NULL default 0, 
            attribute_id      integer unsigned NOT NULL default 0, 
            value_tinyint     tinyint unsigned NOT NULL default 0, 
            value_integer     integer unsigned NOT NULL default 0, 
            value_decimal     decimal(15,5) default NULL,
            value_string      varchar(254) NOT NULL default '', 
            value_text        text, 
            PRIMARY KEY  (id), 
            KEY i_tag_ids (item_id,attribute_id)
        )";
        if (!$q->run($query)) return;

    # --------------------------------------------------------
    #
    # Set up masks
    #
        xarRegisterMask('ViewEAV','All','eav','All','All','ACCESS_OVERVIEW');
        xarRegisterMask('ReadEAV','All','eav','All','All','ACCESS_READ');
        xarRegisterMask('CommentEAV','All','eav','All','All','ACCESS_COMMENT');
        xarRegisterMask('ModerateEAV','All','eav','All','All','ACCESS_MODERATE');
        xarRegisterMask('EditEAV','All','eav','All','All','ACCESS_EDIT');
        xarRegisterMask('AddEAV','All','eav','All','All','ACCESS_ADD');
        xarRegisterMask('ManageEAV','All','eav','All','All','ACCESS_DELETE');
        xarRegisterMask('AdminEAV','All','eav','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up privileges
    #
        xarRegisterPrivilege('ViewEAV','All','eav','All','All','ACCESS_OVERVIEW');
        xarRegisterPrivilege('ReadEAV','All','eav','All','All','ACCESS_READ');
        xarRegisterPrivilege('CommentEAV','All','eav','All','All','ACCESS_COMMENT');
        xarRegisterPrivilege('ModerateEAV','All','eav','All','All','ACCESS_MODERATE');
        xarRegisterPrivilege('EditEAV','All','eav','All','All','ACCESS_EDIT');
        xarRegisterPrivilege('AddEAV','All','eav','All','All','ACCESS_ADD');
        xarRegisterPrivilege('ManageEAV','All','eav','All','All','ACCESS_DELETE');
        xarRegisterPrivilege('AdminEAV','All','eav','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Create DD objects
    #
        $module = 'eav';
        $objects = array(
                         );

        if(!xarModAPIFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

    # --------------------------------------------------------
    #
    # Set up modvars
    #
        $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'eav'));
        $module_settings->initialize();

        // Add variables like this next one when creating utility modules
        // This variable is referenced in the xaradmin/modifyconfig-utility.php file
        // This variable is referenced in the xartemplates/includes/defaults.xd file
        xarModVars::set('eav', 'defaultmastertable','eav_attributes');

    # --------------------------------------------------------
    #
    # Set up hooks
    #

        return true;
    }

    function eav_upgrade()
    {
        return true;
    }

    function eav_delete()
    {
        $this_module = 'eav';
        return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => $this_module));
    }

?>
