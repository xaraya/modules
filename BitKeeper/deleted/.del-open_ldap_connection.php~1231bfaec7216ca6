<?php
/**
 * File: $Id$
 * 
 * AuthLDAP User API
 * 
 * @package authentication
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @author Chris Dudley <miko@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * open ldap connection
 * @private
 * @author Richard Cave
 * @returns int
 * @return LDAP link identifier on connect, false otherwise
 */
function authldap_userapi_open_ldap_connection()
{
    // Make sure that LDAP is available before trying to connect
    if (!function_exists('ldap_connect'))
        return;

    $ldapconfig['server'] = xarModGetVar('authldap','server');
    $ldapconfig['portnumber'] = xarModGetVar('authldap','port_number');

    if ($ldapconfig['portnumber'])
        $connect=ldap_connect($ldapconfig['server'],$ldapconfig['portnumber']);
    else {
        // connect to default port 389
        $connect=ldap_connect($ldapconfig['server']);
    }

    if (!$connect) {
        error_log("LDAP Error: Connection to " . $ldapconfig['server'] . "failed");
        return false;
    }

    return $connect;
}

?>
