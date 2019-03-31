<?php
/**
 * Karma Module
 *
 * @package modules
 * @subpackage karma
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 *
 * Initialise or remove the karma module
 *
 */

    sys::import('xaraya.structures.query');

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
            timelasthit       int unsigned NOT NULL default '0', 
            timecreated       int unsigned NOT NULL default '0', 
            role_id           int unsigned NOT NULL default '0', 
            count             int unsigned NOT NULL default '0', 
            state             tinyint unsigned NOT NULL default '3', 
            PRIMARY KEY  (id), 
            KEY i_tag_name (name), 
            KEY i_tag_timelasthit (timelasthit), 
            KEY i_tag_timecreated (timecreated), 
            KEY i_tag_role_id (role_id), 
            KEY i_tag_state (state), 
            KEY i_tag_count (count) 
        )";
        if (!$q->run($query)) return;
  
        $query = "DROP TABLE IF EXISTS " . $prefix . "_karma_posts";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_karma_posts (
            id                integer unsigned NOT NULL auto_increment,
            module_id         int unsigned NOT NULL default '0',
            itemtype          int unsigned NOT NULL default '0',
            itemid            int unsigned NOT NULL default '0',
            tag_id            int unsigned NOT NULL default '0', 
            user_id           int unsigned NOT NULL default '0', 
            timecreated       int unsigned NOT NULL default '0', 
            timemodified      int unsigned NOT NULL default '0', 
            text              text,
            count             int unsigned NOT NULL default '0', 
            state             tinyint unsigned NOT NULL default '3', 
            PRIMARY KEY  (id), 
            KEY i_posts_module_id (module_id), 
            KEY i_posts_itemtype (itemtype), 
            KEY i_posts_itemid (itemid), 
            KEY i_posts_state (state), 
            KEY i_posts_user_id (user_id), 
            KEY i_posts_count (count)
        )";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_karma_users";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_karma_users (
            id                integer unsigned NOT NULL auto_increment,
            tagcount          int unsigned NOT NULL default '0', 
            postcount         int unsigned NOT NULL default '0', 
            timelasttag       int unsigned NOT NULL default '0', 
            timelastpost      int unsigned NOT NULL default '0', 
            PRIMARY KEY  (id), 
            KEY i_users_tagcount (tagcount), 
            KEY i_users_postcount (postcount), 
            KEY i_users_timelasttag (timelasttag),
            KEY i_users_timelastpost (timelastpost) 
        )";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_karma_tags_posts";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_karma_tags_posts (
            tag_id            int unsigned NOT NULL default '0', 
            post_id           int unsigned NOT NULL default '0', 
            KEY i_tags_posts_tag_id (tag_id), 
            KEY i_tags_posts_post_id (post_id) 
        )";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_karma_subscriptions";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_karma_subscriptions (
            tag_id            int unsigned NOT NULL default '0', 
            user_id           int unsigned NOT NULL default '0', 
            KEY i_subscriptions_tag_id (tag_id), 
            KEY i_subscriptions_user_id (user_id) 
        )";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_karma_visits";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_karma_visits (
            tag_id            int unsigned NOT NULL default '0', 
            user_id           int unsigned NOT NULL default '0', 
            timelastvisit     int unsigned NOT NULL default '0', 
            KEY i_visits_tag_id (tag_id), 
            KEY i_visits_user_id (user_id) ,
            KEY i_visits_timelastvisit (timelastvisit) 
        )";
        if (!$q->run($query)) return;

# --------------------------------------------------------
    #
    # Set up masks
    #
        xarRegisterMask('ViewKarma','All','karma','All','All','ACCESS_OVERVIEW');
        xarRegisterMask('ReadKarma','All','karma','All','All','ACCESS_READ');
        xarRegisterMask('ManageKarma','All','karma','All','All','ACCESS_DELETE');
        xarRegisterMask('AdminKarma','All','karma','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up privileges
    #
        xarRegisterPrivilege('ManageKarma','All','karma','All','All','ACCESS_DELETE');
        xarRegisterPrivilege('AdminKarma','All','karma','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up modvars
    #
        xarModVars::set('karma', 'itemsperpage', 20);
        xarModVars::set('karma', 'useModuleAlias',0);
        xarModVars::set('karma', 'aliasname','Karma');

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

        if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

    # --------------------------------------------------------
    #
    # Set up hooks
    #
/* Remove for now
        // This is a GUI hook for the roles module that enhances the roles profile page
        if (!xarModRegisterHook('item', 'usermenu', 'GUI',
                'karma', 'user', 'usermenu')) {
            return false;
        }
*/
        xarMod::apiFunc('modules', 'admin', 'enablehooks',
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
        return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $this_module));
    }

?>
