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
 * xarldap_admin_usersearch 
 *
 * Search for a user on the LDAP server 
 *
 * @author  Richard Cave <rcave@xaraya.com>
 * @access  public
 * @param   none 
 * @return  returns true on success or false on failure
 * @throws  none
 * @todo    none
*/
function xarldap_admin_usersearch()
{
    // Security check
    if(!xarSecurityCheck('AdminXarLDAP')) return;
    
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Specify type of search to perform
    $data['type'] = 'user'; 
    
    // Return the template variables defined in this function
    return $data;
}

?>
