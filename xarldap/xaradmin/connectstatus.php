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
 * xarldap_admin_connectstatus: 
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
function xarldap_admin_connectstatus()
{
    // Confirm authorization key 
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new #(1) item',
                    'xarldap');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return false;
    }

    // Assign result to false until connection validated
    $data['result'] = false;
    
    // Include xarldap class
    include_once 'modules/xarldap/xarldap.php';

    // Create new LDAP object
    $ldap = new xarldap();

    // Get server
    $ldap->get_parameters(); 
    $data['server'] = xarVarPrepForDisplay($ldap->server);

    // Make sure LDAP extension exists
    if (!$ldap->exists())
        return $data;
    
    // Open ldap connection
    if (!$ldap->open())
        return $data;

    // Bind to LDAP server
    $bindResult = $ldap->bind_to_server();
    if (!$bindResult)
        return $data;

    // Close LDAP connection
    $ldap->close();

    // Success
    $data['result'] = true;

    // Return the template variables defined in this function
    return $data;
}

?>
