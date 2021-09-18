<?php
/**
 * Realms Module
 *
 * @package modules
 * @subpackage realms module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 *
 * Initialise or remove the realms module
 *
 */

    sys::import('xaraya.structures.query');

    function realms_init()
    {

    # --------------------------------------------------------
        #
        # Set up tables
        #
        $q = new Query();
        $prefix = xarDB::getPrefix();

        # --------------------------------------------------------
        #
        # Table structure for users
        #
        $query = "DROP TABLE IF EXISTS " . $prefix . "_realms_realms";
        if (!$q->run($query)) {
            return;
        }
        $query = "CREATE TABLE " . $prefix . "_realms_realms (
            id                integer unsigned NOT NULL auto_increment,
            name              varchar(254) NOT NULL default '', 
            code              varchar(64) NOT NULL default '', 
            description       text, 
            configuration     text, 
            member_id         integer unsigned NOT NULL default 0, 
            theme_regid       integer unsigned NOT NULL default 0, 
            time_created      integer unsigned NOT NULL default 0, 
            time_modified     integer unsigned NOT NULL default 0, 
            role_id           integer unsigned NOT NULL default 0, 
            state             tinyint(3) NOT NULL default 3, 
            PRIMARY KEY  (id), 
            KEY i_name (name) 
        )";
        if (!$q->run($query)) {
            return;
        }

        $query = "DROP TABLE IF EXISTS " . $prefix . "_realms_members";
        if (!$q->run($query)) {
            return;
        }
        $query = "CREATE TABLE " . $prefix . "_realms_members (
            id                integer unsigned NOT NULL auto_increment,
            name              varchar(254) NOT NULL default '', 
            uname             varchar(254) NOT NULL default '', 
            email             varchar(254) NOT NULL default '', 
            passwd            varchar(254) NOT NULL default '', 
            time_created      integer unsigned NOT NULL default 0, 
            time_modified     integer unsigned NOT NULL default 0, 
            role_link         tinyint(3) NOT NULL default 0,
            role_id           integer unsigned NOT NULL default 0, 
            realm_id          integer unsigned NOT NULL default 0, 
            state             tinyint(3) NOT NULL default 3, 
            PRIMARY KEY  (id), 
            KEY name (name),
            KEY `uname` (`uname`),
            KEY `email` (`email`),
            KEY `state` (`state`)
        )";
        if (!$q->run($query)) {
            return;
        }

        # --------------------------------------------------------
        #
        # Set up masks
        #
        xarMasks::register('ViewRealms', 'All', 'realms', 'All', 'All', 'ACCESS_OVERVIEW');
        xarMasks::register('ReadRealms', 'All', 'realms', 'All', 'All', 'ACCESS_READ');
        xarMasks::register('CommentRealms', 'All', 'realms', 'All', 'All', 'ACCESS_COMMENT');
        xarMasks::register('ModerateRealms', 'All', 'realms', 'All', 'All', 'ACCESS_MODERATE');
        xarMasks::register('EditRealms', 'All', 'realms', 'All', 'All', 'ACCESS_EDIT');
        xarMasks::register('AddRealms', 'All', 'realms', 'All', 'All', 'ACCESS_ADD');
        xarMasks::register('ManageRealms', 'All', 'realms', 'All', 'All', 'ACCESS_DELETE');
        xarMasks::register('AdminRealms', 'All', 'realms', 'All', 'All', 'ACCESS_ADMIN');

        # --------------------------------------------------------
        #
        # Set up privileges
        #
        xarPrivileges::register('ViewRealms', 'All', 'realms', 'All', 'All', 'ACCESS_OVERVIEW');
        xarPrivileges::register('ReadRealms', 'All', 'realms', 'All', 'All', 'ACCESS_READ');
        xarPrivileges::register('CommentRealms', 'All', 'realms', 'All', 'All', 'ACCESS_COMMENT');
        xarPrivileges::register('ModerateRealms', 'All', 'realms', 'All', 'All', 'ACCESS_MODERATE');
        xarPrivileges::register('EditRealms', 'All', 'realms', 'All', 'All', 'ACCESS_EDIT');
        xarPrivileges::register('AddRealms', 'All', 'realms', 'All', 'All', 'ACCESS_ADD');
        xarPrivileges::register('ManageRealms', 'All', 'realms', 'All', 'All', 'ACCESS_DELETE');
        xarPrivileges::register('AdminRealms', 'All', 'realms', 'All', 'All', 'ACCESS_ADMIN');

        # --------------------------------------------------------
        #
        # Create DD objects
        #
        $module = 'realms';
        $objects = [
                    'realms_realms',
                    'realms_members',
                         ];

        if (!xarMod::apiFunc('modules', 'admin', 'standardinstall', ['module' => $module, 'objects' => $objects])) {
            return;
        }
        # --------------------------------------------------------
        #
        # Set up modvars
        #
        $module_settings = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'realms']);
        $module_settings->initialize();

        // Add variables like this next one when creating utility modules
        // This variable is referenced in the xaradmin/modifyconfig-utility.php file
        // This variable is referenced in the xartemplates/includes/defaults.xd file
        xarModVars::set('realms', 'defaultmastertable', 'realms_realms');
        xarModVars::set('realms', 'link_role', 0);
        xarModVars::set('realms', 'default_realm', 0);

        # --------------------------------------------------------
        #
        # Add a user for the default realm
        #
        /*
            $realm = DataObjectMaster::getObject(array('name' => 'realms_realms'));
            $realm->getItem(array('itemid' => 1));
            $groupid = $realm->properties['usergroup']->value;
            $user = xarRoles::ufindRole('realm1user');
            if (empty($user)) {
                $user = DataObjectMaster::getObject(array('name' => 'roles_users'));
                $rolefields['role_type'] = xarRoles::ROLES_USERTYPE;
                $rolefields['name'] = 'Realm 1 User';
                $rolefields['uname'] = 'realm1user';
                $rolefields['uname'] = 'realm1user';
                $rolefields['password'] = 'realm1';
                $rolefields['parentid'] = $groupid;
                $userid = $user->createItem($rolefields);
            } else {
                $userid = $user->updateItem(array('parentid' => $groupid));
            }
        */
        return true;
    }

    function realms_upgrade()
    {
        return true;
    }

    function realms_delete()
    {
        $groupobject = DataObject::getObjectList(['name' => 'realms_realms']);
        $items = $groupobject->getItems();
        $groupobject = DataObject::getObject(['name' => 'realms_realms']);
        foreach ($items as $item) {
            $groupobject->deleteItem(['itemid' =>$item['id']]);
        }

        $this_module = 'realms';
        return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', ['module' => $this_module]);
    }
