<?php
/*
 * File: $Id: $
 *
 * xarldap 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team 
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage xarldap module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Initialize the xarldap module
 */
function xarldap_init()
{
	// Make sure the LDAP PHP extension is available
    if (!extension_loaded('ldap')) {
        $msg=xarML('Your PHP configuration does not seem to include the required LDAP extension. Please refer to http://www.php.net/manual/en/ref.ldap.php on how to install it.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'MODULE_DEPENDENCY',
                        new SystemException($msg));
        return false;
    }
    
    // Set up module variables
    xarModSetVar('xarldap','server', '127.0.0.1');
    xarModSetVar('xarldap','port_number', '389');
    xarModSetVar('xarldap','bind_dn','o=dept');
    xarModSetVar('xarldap','uid_field', 'cn');
    xarModSetVar('xarldap','search_user_dn', 'true');
    xarModSetVar('xarldap','admin_login', '');
    xarModSetVar('xarldap','admin_password', '');
    xarModSetVar('xarldap','key', '');
    xarModSetVar('xarldap','anonymous_bind', 'true');

    xarRegisterMask('OverviewXarLDAP','All','xarldap','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('AdminXarLDAP','All','xarldap','All','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * Upgrade the xarldap module from an old version
 */
function xarldap_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        //case 1.0:
        //case '1.0.0':
            // Code to upgrade from version 1.0 goes here
            //break;
        //case 2.0:
            // Code to upgrade from version 2.0 goes here
            //break;
        default:
            break;
    }

    // Update successful
    return true;
}

/**
 * Delete the xarldap module
 */
function xarldap_delete()
{
    // Remove module variables
    xarModDelVar('xarldap','server');
    xarModDelVar('xarldap','port_number');
    xarModDelVar('xarldap','bind_dn');
    xarModDelVar('xarldap','uid_field');
    xarModDelVar('xarldap','search_user_dn');
    xarModDelVar('xarldap','admin_login');
    xarModDelVar('xarldap','admin_password');
    xarModDelVar('xarldap','key');
    xarModDelVar('xarldap','anonymous_bind');

    // Remove masks
    xarRemoveMasks('xarldap');

    // Deletion successful
    return true;
}

?>
