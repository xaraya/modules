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
 * search LDAP for user entities
 * @private
 * @author Richard Cave
 * @param args['connect'] open LDAP link connection
 * @param args['entry'] specific entry in LDAP directory
 * @param args['attribute'] attribute searching for (ie 'mail')
 * @returns int
 * @return attribute of entry, nothing otherwise
 */
function authldap_userapi_get_attribute_value($args)
{
    extract($args);

    if (!isset($connect) || !isset($entry) || !isset($attribute)) {
        $msg = xarML('Empty connect (#(1)) or entry (#(2)) or attribute (#(3)).', $connect, $entry, $attribute);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return XARUSER_AUTH_FAILED;
    }

    // what to do with more than one entry for user info?
    //$num_entries = ldap_count_entries($connect,$entry);

    // get attribute value
    $value = $entry[0][$attribute][0];
    return $value;
/*
    for ($i=0; $i<$num_entries; $i++) {  // loop though ldap search result
        error_log("user dn: " . $user_info[$i]['dn']);
        for ($ii=0; $ii<$user_info[$i]['count']; $ii++) {
            $attrib = $user_info[$i][$ii];
            eval("error_log( \$user_info[\$i][\"$attrib\"][0]);"); 
       }
*/
}

?>
