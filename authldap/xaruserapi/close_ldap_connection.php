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
 * Close LDAP connection
 * @private
 * @author Richard Cave
 * @param args['connect'] open LDAP link connection
 * @returns int
 * @return true on success, false otherwise
 */
function authldap_userapi_close_ldap_connection($args)
{
    extract($args);

    if (!isset($connect)) {
        $msg = xarML('Empty LDAP connection');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return XARUSER_AUTH_FAILED;
    }

    // Close LDAP connection
    ldap_close($connect);

    return true;
}

?>
