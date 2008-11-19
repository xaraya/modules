<?php
/**
 *
 * Initialise or remove the karma module
 *
 */

    sys::import('modules.query.class.query');

    function karma_init()
    {

    # --------------------------------------------------------
    #
    # Set tables
    #
        $q = new Query();
        $prefix = xarDB::getPrefix();
        
        $query = "DROP TABLE IF EXISTS " . $prefix . "_karma_tags";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_karma_tags (
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
  
        $query = "DROP TABLE IF EXISTS " . $prefix . "_karma_posts";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_karma_posts (
            id                integer unsigned NOT NULL auto_increment,
            module_id         int default NULL,
            itemtype          int default NULL,
            itemid            int default NULL,
            timecreated       int(11) unsigned NOT NULL default '0', 
            timemodified      int(11) unsigned NOT NULL default '0', 
            role_id           int(11) unsigned NOT NULL default '0', 
            text              text,
            state             tinyint(4) NOT NULL default '1', 
            count             int(11) unsigned NOT NULL default '0', 
            PRIMARY KEY  (id), 
            KEY i_posts_module_id (module_id), 
            KEY i_posts_itemtype (itemtype), 
            KEY i_posts_itemid (itemid), 
            KEY i_posts_state (state), 
            KEY i_posts_role_id (role_id), 
            KEY i_posts_count (count), 
            FULLTEXT KEY text (text) 
        ) TYPE=MyISAM";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_karma_users";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_karma_users (
            id                integer unsigned NOT NULL auto_increment,
            tagcount          int(11) unsigned NOT NULL default '0', 
            postcount         int(11) unsigned NOT NULL default '0', 
            timelasttag       int(11) unsigned NOT NULL default '0', 
            timelastpost      int(11) unsigned NOT NULL default '0', 
            PRIMARY KEY  (id), 
            KEY i_users_tagcount (tagcount), 
            KEY i_users_postcount (postcount), 
            KEY i_users_timelasttag (timelasttag),
            KEY i_users_timelastpost (timelastpost) 
        ) TYPE=MyISAM";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_karma_tags_posts";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_karma_tags_posts (
            tag_id            int(11) unsigned NOT NULL default '0', 
            post_id           int(11) unsigned NOT NULL default '0', 
            KEY i_tags_posts_tag_id (tag_id), 
            KEY i_tags_posts_post_id (post_id) 
        ) TYPE=MyISAM";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_karma_subscriptions";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_karma_subscriptions (
            tag_id            int(11) unsigned NOT NULL default '0', 
            role_id           int(11) unsigned NOT NULL default '0', 
            KEY i_subscriptions_tag_id (tag_id), 
            KEY i_subscriptions_role_id (role_id) 
        ) TYPE=MyISAM";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_karma_visits";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_karma_visits (
            tag_id            int(11) unsigned NOT NULL default '0', 
            role_id           int(11) unsigned NOT NULL default '0', 
            timelastvisit     int(11) unsigned NOT NULL default '0', 
            KEY i_visits_tag_id (tag_id), 
            KEY i_visits_role_id (role_id) ,
            KEY i_visits_timelastvisit (timelastvisit) 
        ) TYPE=MyISAM";
        if (!$q->run($query)) return;

# --------------------------------------------------------
    #
    # Set up masks
    #
        xarRegisterMask('ViewKarma','All','karma','All','All','ACCESS_OVERVIEW');
        xarRegisterMask('ReadKarma','All','karma','All','All','ACCESS_READ');
        xarRegisterMask('CommentFoo','All','foo','All','All','ACCESS_COMMENT');
        xarRegisterMask('ModerateFoo','All','foo','All','All','ACCESS_MODERATE');
        xarRegisterMask('EditFoo','All','foo','All','All','ACCESS_EDIT');
        xarRegisterMask('AddFoo','All','foo','All','All','ACCESS_ADD');

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

    # --------------------------------------------------------
    #
    # Set up modvars
    #
        xarModVars::set('karma', 'itemsperpage', 20);
        xarModVars::set('karma', 'useModuleAlias',0);
        xarModVars::set('karma', 'aliasname','Karma');
        xarModVars::set('foo', 'defaultmastertable','foo_foo');

        // Add variables like this next one when creating utility modules
        // This variable is referenced in the xaradmin/modifyconfig-utility.php file
        // This variable is referenced in the xartemplates/includes/defaults.xd file
    //    xarModVars::set('karma', 'bar', 'Bar');

    # --------------------------------------------------------
    #
    # Create DD objects
    #
        $module = 'karma';
        $objects = array(
                         'karma_tags',
                         );

        if(!xarModAPIFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

    # --------------------------------------------------------
    #
    # Set up hooks
    #
        // This is a GUI hook for the roles module that enhances the roles profile page
        if (!xarModRegisterHook('item', 'usermenu', 'GUI',
                'karma', 'user', 'usermenu')) {
            return false;
        }

        xarModAPIFunc('modules', 'admin', 'enablehooks',
            array('callerModName' => 'karma', 'hookModName' => 'karma'));

        return true;
    }

    function karma_upgrade()
    {
        return true;
    }

    function karma_delete()
    {
        $this_module = 'karma';
        return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => $this_module));
    }

?>
