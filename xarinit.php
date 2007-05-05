<?php

sys::import('modules.query.class.query');

function members_init()
{
    if (!xarVarFetch('createdefaultgroup', 'bool', $createdefaultgroup, true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultgroupname', 'str:1:', $defaultgroupname, 'MembersGroup', XARVAR_NOT_REQUIRED)) return;

# --------------------------------------------------------
#
# Set up tables
#
    $q = new Query();
    $prefix = xarDB::getPrefix();

    $query = "DROP TABLE IF EXISTS " . $prefix . "_members_members";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_members_members (
      id int(10) unsigned default '0' NOT NULL,
      title varchar(20) default '' NOT NULL,
      position varchar(64) default '' NOT NULL,
      firstname varchar(35) default '' NOT NULL,
      middlename varchar(35) default '' NOT NULL,
      lastname varchar(35) default '' NOT NULL,
      address int(10) default '0' NOT NULL,
      memberid int (10) default '0' NOT NULL,
      phone varchar (35) default '' NOT NULL,
      mobilephone varchar (35) default '' NOT NULL,
      fax varchar(35) default '' NOT NULL,
      photo varchar(254) default '' NOT NULL,
      profile text default '',
      PRIMARY KEY  (id)
    ) TYPE=MyISAM";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_members_addresses";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_members_addresses (
      id int(10) unsigned NOT NULL auto_increment,
      location text default '' NOT NULL,
      city varchar(35) default '' NOT NULL,
      state varchar(35) default '' NOT NULL,
      postalcode varchar(35) default '' NOT NULL,
      country_id varchar(2) default NULL,
      PRIMARY KEY  (id)
    ) TYPE=MyISAM";
    if (!$q->run($query)) return;

# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewMembers','All','members','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadMembers','All','members','All','All','ACCESS_READ');
    xarRegisterMask('CommentMembers','All','members','All','All','ACCESS_COMMENT');
    xarRegisterMask('ModerateMembers','All','members','All','All','ACCESS_MODERATE');
    xarRegisterMask('EditMembers','All','members','All','All','ACCESS_EDIT');
    xarRegisterMask('AddMembers','All','members','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteMembers','All','members','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminMembers','All','members','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('ViewMembers','All','members','All','All','ACCESS_OVERVIEW');
    xarRegisterPrivilege('ReadMembers','All','members','All','All','ACCESS_READ');
    xarRegisterPrivilege('CommentMembers','All','members','All','All','ACCESS_COMMENT');
    xarRegisterPrivilege('EditMembers','All','members','All','All','ACCESS_EDIT');
    xarRegisterPrivilege('AddMembers','All','members','All','All','ACCESS_ADD');
    xarRegisterPrivilege('DeleteMembers','All','members','All','All','ACCESS_DELETE');
    xarRegisterPrivilege('AdminMembers','All','members','All','All','ACCESS_ADMIN');
    xarMakePrivilegeRoot('ViewMembers');
    xarMakePrivilegeRoot('ReadMembers');
    xarMakePrivilegeRoot('EditMembers');
    xarMakePrivilegeRoot('CommentMembers');
    xarMakePrivilegeRoot('AddMembers');
    xarMakePrivilegeRoot('DeleteMembers');
    xarMakePrivilegeRoot('AdminMembers');

# --------------------------------------------------------
#
# Set up the default group
#
    if ($createdefaultgroup) {
        $role = xarFindRole($defaultgroupname);
        $group = DataObjectMaster::getObject(array('name' => 'roles_groups'));
        $rolefields = array(
                        'itemid' => 0,
                        'users' => 0,
                        'regdate' => time(),
                        'state' => ROLES_STATE_ACTIVE,
                        'valcode' => 'createdbysystem',
                        'authmodule' => xarMod::getID('members'),
        );
        if (empty($role)) {
            $everybody = xarFindRole('Everybody');
            $rolefields['role_type'] = ROLES_GROUPTYPE;
            $rolefields['name'] = $defaultgroupname;
            $rolefields['uname'] = $defaultgroupname;
            $rolefields['parentid'] = $everybody->getID();
            $defaultgroup = $group->createItem($rolefields);
        } else {
            $defaultgroup= $role->getID();
        }
    } else {
        $defaultgroup = xarModVars::get('members', 'defaultgroup');
    }

# --------------------------------------------------------
#
# Set up modvars
#
    $roman = array(
        'A', 'B', 'C', 'D', 'E', 'F',
        'G', 'H', 'I', 'J', 'K', 'L',
        'M', 'N', 'O', 'P', 'Q', 'R',
        'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z'
    );
    xarModVars::set('members', 'alphabet', serialize($roman));
    xarModVars::set('members', 'itemsperpage', 20);
    xarModVars::set('members', 'supportshorturls', false);
    xarModVars::set('members', 'useModuleAlias',0);
    xarModVars::set('members', 'aliasname','Members');
    xarModVars::set('members', 'defaultviewtype', 'viewall');
    xarModVars::set('members', 'defaultgroup', $defaultgroup);
    xarModVars::set('members', 'showalltab', 1);
    xarModVars::set('members', 'showothertab', 1);
    xarModVars::set('members', 'showactiveall', 0);
    xarModVars::set('members', 'defaultselectkey', 'last_name');
    xarModVars::set('members', 'usernamevars', 'lastname.firstname.id');

# --------------------------------------------------------
#
# Set up hooks
#
    // This is a GUI hook for the roles module that enhances the roles profile page
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'members', 'user', 'usermenu')) {
        return false;
    }
# --------------------------------------------------------
#
# Create a parent category
#

# --------------------------------------------------------
#
# Create DD objects
#
    $module = 'members';
    $objects = array(
                   'members_members',
                   'members_addresses',
                     );
    if(!xarModAPIFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

    // Save the object reference
    $info = DataObjectMaster::getObjectInfo(array('name' => 'members_members'));
    xarModVars::set('members', 'object', $info['objectid']);

    return true;
}

function members_upgrade()
{
    return true;
}

function members_delete()
{
    return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => 'members'));
}

?>