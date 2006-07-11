<?php
/**
 * AuthLDAP Initialisation
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @link http://xaraya.com/index.php/release/50.html
 * @author Chris Dudley <miko@xaraya.com>
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * Initialisation function
*/
function authldap_init()
{
    // Make sure the LDAP PHP extension is available
    if (!extension_loaded('ldap')) {
        $msg=xarML('Your PHP configuration does not seem to include the required LDAP extension. Please refer to http://www.php.net/manual/en/ref.ldap.php on how to install it.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION,'MODULE_DEPENDENCY',
                        new SystemException($msg));
        return;
    }

    // Set up module variables
    xarModSetVar('authldap','add_user', 'true');
    xarModSetVar('authldap','add_user_uname', 'sn');
    xarModSetVar('authldap','add_user_email', 'mail');
    xarModSetVar('authldap','store_user_password', 'true');
    xarModSetVar('authldap','failover', 'true');
    authldap_initgroupsvars();

    // Define mask definitions for security checks
    xarRegisterMask('AdminAuthLDAP','All','authldap','All','All','ACCESS_ADMIN');
    xarRegisterMask('ReadAuthLDAP','All','authldap','All','All','ACCESS_READ');

    // Do not add authldap to Site.User.AuthenticationModules in xar_config_vars here
/*
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModulesUpdate = array();

    // insert authldap right before authsystem
    foreach ($authModules as $authType) {
        if ($authType == 'authsystem') {
            $authModulesUpdate[] = 'authldap';
        }// if
        $authModulesUpdate[] = $authType;
    }// foreach

    // save the setting
    xarConfigSetVar('Site.User.AuthenticationModules',$authModulesUpdate);
*/

    authldap_createdb();
    
    // Initialization successful
    return true;
}

/**
 * Module upgrade function
 *
 *
 */
function authldap_upgrade($oldVersion)
{
  switch($oldVersion) {
  case '1.0':
    authldap_createdb();
    authldap_initgroupsvars();
    break;
  }
  return true;
}

/**
 * module removal function
*/
function authldap_delete()
{
  // Get database information
  $dbconn =& xarDBGetConn();
  $tables =& xarDBGetTables();
  
  //Load Table Maintainance API
  xarDBLoadTableMaintenanceAPI();
  
  // Generate the SQL to drop the table using the API
  $query = xarDBDropTable($tables['authldap_usercache']);
  if (empty($query)) return;
  if (!$dbconn->Execute($query)) return;
  
  // Remove module variables
  // Done automatically
  //     xarModDelVar('authldap','add_user');
  //     xarModDelVar('authldap','add_user_uname');
  //     xarModDelVar('authldap','add_user_email');
  //     xarModDelVar('authldap','store_user_password');
  //     xarModDelVar('authldap','failover');
  
  // Remove authldap to Site.User.AuthenticationModules in xar_config_vars
  $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
  $authModulesUpdate = array();
  
  // Loop through current auth modules and remove 'authldap'
  foreach ($authModules as $authType) {
    if ($authType != 'authldap')
      $authModulesUpdate[] = $authType;
  }
  xarConfigSetVar('Site.User.AuthenticationModules',$authModulesUpdate);
  
  // Deletion successful
  return true;
}

function authldap_createdb() 
  {
  // Get database setup
  $dbconn =& xarDBGetConn();
  $tables =& xarDBGetTables();
  
  //Load Table Maintainance API
  xarDBLoadTableMaintenanceAPI();

  $sitePrefix = xarDBGetSiteTablePrefix();

  // prefix_roles
  $query =
    xarDBCreateTable($tables['authldap_usercache'],
             array('role_id' => array('type' => 'integer',
                         'null' => false,
                         'default' => '0'),
               'uid_field' => array('type' => 'varchar',
                        'size' => 255,
                        'null' => false,
                        'default' => ''),
               'attr_name' => array('type' => 'varchar',
                        'size' => 255,
                        'null' => false,
                        'default' => ''),
               'attr_value' => array('type' => 'varchar',
                         'size' => 255,
                         'null' => false,
                         'default' => ''),
));
  if (!$dbconn->Execute($query)) return;

}

function authldap_initgroupsvars() 
  {
  include_once('modules/authldap/xarincludes/default_variables.php');
  foreach($default_groups_variables as $variable => $default_value)
    xarModSetVar('authldap', $variable, $default_value);
}
?>