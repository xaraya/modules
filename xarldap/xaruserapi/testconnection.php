<?php
/**
 * File: $Id$
 *
 * XarLDAP Administration
 * 
 * @package authentication
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage xarldap
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * xarldap_userapi_testconnection
 *
 * Connect to an LDAP server
 *
 * @author  Richard Cave <rcave@xaraya.com>
 * @access  public
 * @param   none 
 * @return  returns true on success or false on failure
 * @throws  none
 * @todo    none
*/
function xarldap_userapi_testconnection()
{
    // Include xarldap class
    include_once 'modules/xarldap/xarldap.php';

    // Assign result to false until connection validated
    $result = false;
    
    // Create new LDAP object
    $ldap = new xarLDAP();

    // Make sure LDAP extension exists
    if (!$ldap->exists())
        return $result;
    
    // Open ldap connection
    if (!$ldap->open())
        return $result;

    // Bind to LDAP server
    $bindResult = $ldap->bind_to_server();
    if (!$bindResult)
        return $result;

    // Close LDAP connection
    $ldap->close();

    // Success
    $result = true;

    // Return the template variables defined in this function
    return $result;
}

?>
