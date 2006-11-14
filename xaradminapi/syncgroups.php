<?php
/**
 * AuthLDAP Administrative Display Functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @link http://xaraya.com/index.php/release/50.html
 * @author Sylvain Beucler <beuc@beuc.net>
*/

/*
LONG TERM TODOs:
- Support LDAP 'profiles', instead of unique LDAP configuration. That
  way, the LDAP server (or just its settings) can be different for
  users and groups.

- Only add people to the default LDAP group if they are orphan _after
  the whole updating process took place_.

- If user_ref_attrtype != 'dn', one single ldap query can grab all the
  children groups (or users) info. For example, if your
  user_ref_attrtype is 'cn', you can use a filter like
  '(|(cn=name1)(cn=name2)(cn=name3)...)'. So the number of queries
  would be (2 * height(groups_tree)) instead of (len($roles)). But
  again, that doesn't work if you user_ref_type is 'dn'. Beware of
  potential length limits, e.g. if you grab thousands of member
  profiles at once.

- Initiate a sync on a user-centric basis? ie. when testing a user
  privilege, only update the user's membership in his ancestor groups
  tree. Maybe more efficient, with a different caching sync time.

- Make the LDAP groups read-only in the web interface; or
  alternatively, make Xaraya actually update the LDAP server if there
  are local changes.
*/

/**
 * Get the groups defined in the configuration and mirror them as
 * Xaraya roles, setting up membership of LDAP Xaraya users
 * accordingly.
 *
 * @author Sylvain Beucler
 * @return Array array containing a status
 */
function authldap_adminapi_syncgroups ()
{
  include_once 'modules/xarldap/xarldap.php';
  include_once 'modules/roles/xarroles.php';

  // Variables
  include_once('modules/authldap/xarincludes/default_variables.php');
  foreach($default_groups_variables as $variable => $value) {
    xarVarSetCached('authldap', $variable, xarModGetVar('authldap', $variable));
  }

  $group_name_attrname = xarVarGetCached('authldap', 'group_name_attrname');
  $group_ref_attrtype = xarVarGetCached('authldap', 'group_ref_attrtype');
  $ldap_base4groups = xarVarGetCached('authldap', 'ldap_base4groups');


  // Establish the LDAP connection
  $ldap = new xarLDAP();
  if (!$ldap->open()) return false;
  if (!$ldap->bind_to_server()) return false;
  xarVarSetCached('authldap', 'uid_field', $ldap->uid_field);
  xarVarSetCached('authldap', 'ldap_base4users', $ldap->bind_dn);

  // Get default group container
  $roles = new xarRoles();
  xarVarSetCached('authldap', 'defaultgroup4groups',
                  $roles->getRole(xarVarGetCached('authldap','defaultgroup4groups_id')));
  $defaultgroup4groups = xarVarGetCached('authldap', 'defaultgroup4groups');

  // Get default user container
  $defaultgroup = xarModGetVar('authldap','defaultgroup');
  xarVarSetCached('authldap', 'defaultgroup4users',
                  authldap_get_active_group_by_name($defaultgroup));


  // Loads the users info cache
  xarVarSetCached('authldap', 'user_cache', authldap_build_user_cache($ldap));


  // Bootstrap the list of trees to expand
  $processed_group = array();
  $groups_to_empty = array();
  $processed_users = array();
  $ldap_groups_to_import = preg_split('/ *, */', xarVarGetCached('authldap', 'ldap_groups_to_import'));


  // Import each group
  foreach ($ldap_groups_to_import as $group) {
    $result = authldap_search($ldap, $ldap_base4groups, $group_name_attrname, $group,
                 array($group_ref_attrtype));
    $entries = $ldap->get_entries($result);

    // Parses all returns group (there can be several matches if using a wildcard '*')
    for($i = 0; $i < $entries['count']; $i++) {
      $group_ref = $entries[$i][$group_ref_attrtype];
      authldap_build_tree($ldap, $processed_group, $groups_to_empty, $processed_users, $group_ref, $defaultgroup4groups);
    }
  }

  // Group clean-up: if a previously-existing group is now orphan, it
  // won't be updated anymore. No need to let outdated information
  // floating around. We do not remove the group, though, so that
  // attached privileges are not lost if the group is reused again in
  // the future.
  foreach($groups_to_empty as $role)
    if (count($role->getParents()) == 0)
      authldap_empty_group($role);

  return true;
}


/**
 * Recursively create a group's tree
 */
function authldap_build_tree($ldap, &$processed_group, &$groups_to_empty, &$processed_users, $group, $parent_group)
  {
  $ldap_base4groups = xarVarGetCached('authldap', 'ldap_base4groups');
  $defaultgroup4groups = xarVarGetCached('authldap', 'defaultgroup4groups');
  $group_name_attrname = xarVarGetCached('authldap', 'group_name_attrname');
  $group_ref_attrname = xarVarGetCached('authldap', 'group_ref_attrname');
  $group_ref_attrtype = xarVarGetCached('authldap', 'group_ref_attrtype');
  $user_ref_attrname = xarVarGetCached('authldap', 'user_ref_attrname');;

  // Do not import a group twice. A group can appear multiple times in
  // the imported groups and subgroups
  if (isset($processed_group[$group])) {
    $parent_group->addMember($processed_group[$group]);
    return true;
  }

  // Grab the group's information
  $result = authldap_search($ldap, $ldap_base4groups, $group_ref_attrtype, $group,
                            array($group_name_attrname, // attributes to grab
                                  $group_ref_attrname,
                                  $user_ref_attrname));
  if (!authldap_search_succeeded($result)) { // Dead reference?
    xarErrorFree();
    return false;
  }
  $entries = $ldap->get_entries($result);
  $group_name = $ldap->get_attribute_value($entries, $group_name_attrname);
  $group_role = authldap_get_active_group_by_name($group_name);


  // Backup the previous list of children groups, for later
  // comparison. Then clean-up before import
  $previous_children_groups = array();
  if (!isset($group_role)) {
    $group_role = authldap_create_group($group_name);
  } else {
    if ($group_role->getAuthModule() == 'authldap') {
      $roles = new xarRoles();
      $previous_children_groups = $roles->getsubgroups($group_role->getId());
      // Clean the group
      authldap_empty_group($group_role);
    } else {
      // Do not modify a non-LDAP group (especially the one called
      // "Administrators"..)
      return true;
    }
  }
  $parent_group->addMember($group_role);
  $processed_group[$group] = $group_role;


  // Add members to group
  if (isset($entries[0][$user_ref_attrname])) {
    $user_ref_list = $entries[0][$user_ref_attrname];
    for ($i = 0; $i < $user_ref_list['count']; $i++) {
      $user_roles = authldap_get_user_roles($processed_user, $user_ref_list[$i]);
      if (!empty($user_roles)) {
      foreach($user_roles as $role)
           $group_role->addMember($role);
      }
    }
  }

  // Recursively create subgroups
  $children_groups = array();
  if (isset($entries[0][$group_ref_attrname])) {
    $group_ref_list = $entries[0][$group_ref_attrname];
    for ($i = 0; $i < $group_ref_list['count']; $i++) {
      $success = authldap_build_tree($ldap, $processed_group, $groups_to_empty, $processed_users, $group_ref_list[$i], $group_role);
      if ($success) {
        $subgroup_role = $processed_group[$group_ref_list[$i]];
        $children_groups[$subgroup_role->getName()] = 1;
      }
    }
  }

  // Check the children groups that were removed from this parent
  // group and mark them for potential reset, if they become orphan
  // after the sync
  $defaultgroup4groups = xarVarGetCached('authldap', 'defaultgroup4groups');
  foreach($previous_children_groups as $old_group) {
    if (!isset($children_groups[$old_group['name']])) {
      $old_group_role = $roles->getRole($old_group['uid']);
      $group_role->removeMember($old_group_role);
      // in case the group becomes orphan:
      $defaultgroup4groups->addMember($old_group_role);
      array_push($groups_to_empty, $old_group_role);
    }
  }

  return true;
}


/**
 * Wrapper to search either a normal attribute, or a distringuished
 * name (dn)
 */
function authldap_search($ldap, $base, $attr, $value, $attributes_to_grab)
  {
  $filter = '';
  // 'dn' cannot be used as a filter attribute name :/
  if ($attr == 'dn') {
    $base = $value;
    $filter = '(objectClass=*)'; // I _have_ to pass a filter
  } else {
    $filter = '(' . $attr . '=' . $value . ')';
  }
  return $ldap->search($base, $filter, $attributes_to_grab);
}

/**
 * Tells if the LDAP search occured without error
 */
function authldap_search_succeeded($result)
  {
  return (isset($result)
          || xarCurrentErrorType() != XAR_SYSTEM_EXCEPTION
          || xarCurrentErrorId() != 'NO_PERMISSION');
}

/**
 * Create a new LDAP group
 */
function authldap_create_group($group_name)
  {
  $defaultgroup = xarVarGetCached('authldap', 'defaultgroup4groups');
  $roles = new xarRoles();
  $roles->makeGroup($group_name);
  $role = authldap_get_active_group_by_name($group_name);
  $role->setAuthModule('authldap');
  $role->update();
  $defaultgroup->addMember($role);
  return $role;
}

/**
 * Remove a group's members
 */
function authldap_empty_group($role)
  {
  $defaultgroup = xarVarGetCached('authldap', 'defaultgroup4users');
  $member_users = $role->getUsers(ROLES_STATE_CURRENT);
  foreach ($member_users as $member) {
    $role->removeMember($member);
    // In case the member is orphan, let's add it to the default
    // LDAP group (no user should be left orphan, else it may
    // disappear from the webui)
    $defaultgroup->addMember($member);
  }
}

/**
 * Get the role(s) associated with a LDAP group's member.
 *
 * Where I work, the Xaraya identifier is the person e-mail
 * address. Since a person can get several e-mail addresses, he may
 * get several Xaraya accounts, so this function may return several
 * *roles. It is ugly to use a variable parameter as a login, but
 * *well... now I have to take that into account.
 */
function authldap_get_user_roles(&$processed_users, $user_ref)
  {
  $user_ref_attrtype = xarVarGetCached('authldap', 'user_ref_attrtype');
  $uid_attrname = xarModGetVar('authldap', 'uid_field');
  $user_cache = xarVarGetCached('authldap', 'user_cache');

  $roles = new xarRoles();
  // Get the roles from the already searched accounts, or from the
  // user cache built at the beginning.
  if (empty($processed_users[$user_ref])) {
    if (!empty($user_cache[$user_ref])) {
      $result = array();
      foreach ($user_cache[$user_ref] as $user_id)
        array_push($result, $roles->getRole($user_id));
      $processed_users[$user_ref] = $result;
    } else {
      // Not in the cache? Then it doesn't match a Xaraya account
      $processed_users[$user_ref] = NULL;
    }
  }
  return $processed_users[$user_ref];
}

/**
 * xarRoles' lookup function matches deleted roles, so we have to use
 * this workaround.
 */
function authldap_get_active_group_by_name($role_name)
  {
  $type_group = 1;
  $role_info = xarModAPIFunc('roles', 'user', 'get',
                              array('state' => ROLES_STATE_CURRENT,
                                    'type' => $type_group,
                                    'name' => $role_name));
  $roles = new xarRoles();
  $role = $roles->getRole($role_info['uid']);
  if (isset($role))
    return $role;
  return NULL;
}


/**
 * Grabs the cached LDAP identifiers for Xaraya accounts. Returns 2
 * arrays, for each direction of the association (key->valueS and
 * value->key)
 */
function authldap_load_user_cache_from_db()
  {
  // Get database setup
  $dbconn =& xarDBGetConn();
  $xartable =& xarDBGetTables();
  $table = $xartable['authldap_usercache'];
  $uid_attrname = xarVarGetCached('authldap', 'uid_field');
  $attr_name = xarVarGetCached('authldap', 'user_ref_attrtype');

  // Get values - cached values may change depending on the LDAP
  // settings, hence the 2 additional fields
  $query = "SELECT role_id, attr_value FROM $table WHERE uid_field = ? AND attr_name = ?";
  $bindvars = array($uid_attrname, $attr_name);
  $result =& $dbconn->Execute($query, $bindvars);

  if (!$result) return;

  $cache = array();
  $reverse_cache = array();
  // Load the DB
  while (!$result->EOF) {
    list($role_id, $attr_value) = $result->fields;
    // again, multiple roles can match a LDAP user
    if (isset($cache[$attr_value])) {
      array_push($cache[$attr_value], $role_id);
    } else {
      $cache[$attr_value] = array($role_id);
    }
    // also get the association the other way around
    $reverse_cache[$role_id] = $attr_value;
    $result->MoveNext();
  }
  $result->Close();

  // Return both arrays (to get with list())
  return array($cache, $reverse_cache);
}

/**
 * Load the user cache and synchronize it before returning it
 */
function authldap_build_user_cache($ldap)
  {
  // Get the current cache
  list($cache, $reverse_cache) = authldap_load_user_cache_from_db();


  // Synchronize the cache
  // Get current users
  $roles = new xarRoles();
  $users = xarModAPIFunc('roles', 'user', 'getall',
       array('selection' => "AND xar_auth_module='authldap'"));

  $reverse_users = array();
  foreach($users as $user)
    $reverse_users[$user['uid']] = 1;

  // Remove obsolete users
  foreach ($reverse_cache as $uid => $ref) {
    if (!isset($reverse_users[$uid])) {
      authldap_remove_from_user_cache($ldap, $uid);
    }
  }

  // Add new users
  foreach ($users as $user) {
    if (!isset($reverse_cache[$user['uid']])) {
      $user_ref = authldap_add_to_user_cache($ldap, $user['uid'], $user['uname']);
    }
  }


  // Reload the updated cache
  list($cache) = authldap_load_user_cache_from_db();

  return $cache;
}

/**
 * Add a new user in the DB user cache
 */
function authldap_add_to_user_cache($ldap, $user_id, $uname)
  {
  // Module variables
  $user_ref_attrtype = xarVarGetCached('authldap', 'user_ref_attrtype');
  $uid_attrname = xarVarGetCached('authldap', 'uid_field');
  $ldap_base4users = xarVarGetCached('authldap', 'ldap_base4users');

  // Get user info
  $result = authldap_search($ldap, $ldap_base4users, $uid_attrname, $uname, array($user_ref_attrtype));

  if (!authldap_search_succeeded($result)) { // Dead reference?
    xarErrorFree();
    // Do nothing
  } else {
    $entries = $ldap->get_entries($result);
    $user_ref = $ldap->get_attribute_value($entries, $user_ref_attrtype);
    if (isset($user_ref)) {
      // Get database setup
      $dbconn =& xarDBGetConn();
      $xartable =& xarDBGetTables();
      $table = $xartable['authldap_usercache'];

      // Insert new value
      $query = "INSERT INTO $table (role_id, uid_field, attr_name, attr_value)"
        . " VALUES (?,?,?,?)";
      $attr_name = xarVarGetCached('authldap', 'user_ref_attrtype');
      $bindvars = array($user_id, $uid_attrname, $attr_name, $user_ref);
      $result =& $dbconn->Execute($query, $bindvars);
      if ($result)
        return $user_ref;
    }
  }
  return;
}

/**
 * Remove a cached entry refering to a non-existing Xaraya user
 */
function authldap_remove_from_user_cache($ldap, $user_id)
  {
  // Get database setup
  $dbconn =& xarDBGetConn();
  $xartable =& xarDBGetTables();
  $table = $xartable['authldap_usercache'];

  // Insert new value
  $query = "DELETE FROM $table WHERE role_id = ?";
  $bindvars = array($user_id);
  $dbconn->Execute($query, $bindvars);
}
?>