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
 * xarldap_userapi_search: 
 *
 * Search for a user on the LDAP server 
 *
 * @author  Richard Cave <rcave@xaraya.com>
 * @access  public
 * @param   'type' type of search to perform
 * @param   'value' value to search for (e.g. a username) 
 * @return  returns true on success or false on failure
 * @throws  none
 * @todo    none
*/
function xarldap_userapi_search($args)
{
    // Get arguments from argument array
    extract ($args);

    // Default search type to user if none provided
    if (!isset($search))
        $search = 'user';

    // Include xarldap class
    include_once 'modules/xarldap/xarldap.php';

    // Create new LDAP object
    $ldap = new xarldap();

    switch ($search) {
        case 'user':
            // Make sure LDAP extension exists
            $result = $ldap->user_search($value);
            break;

        default:
            $result = false;
            break;
    }
   

   // Return the template variables defined in this function
    return $result;
}

?>
