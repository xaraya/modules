<?php
/**
 * File: $Id$
 *
 * XarLDAP Administrative Test Configuration
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
 * xarldap_adminapi_testconfig: 
 *
 * Test the xarldap configuration
 *
 * @author  Richard Cave <rcave@xaraya.com>
 * @access  public
 * @param   none 
 * @return  returns true on success or false on failure
 * @throws  none
 * @todo    none
*/
function xarldap_adminapi_testconfig()
{
    // Create LDAP object
    include_once 'modules/xarldap/xarldap.php';
    $ldap = new xarLDAP();

    // Make sure LDAP extension exists
    if (!$ldap->exists())
        return false;

    // Open ldap connection
    if (!$ldap->open())
        return false;

    // Bind to LDAP server
   $bindResult = $ldap->bind_to_server();
    if (!$bindResult)
        return false;

    // close LDAP connection
    $ldap->close();

    // Success
    return true;
}

?>
