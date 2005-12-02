<?php
/**
* Get all subscribers
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* get all subscribers
*
* pid optional
*/
function ebulletin_userapi_getsubscriberemails($args)
{
    // security check
    if (!xarSecurityCheck('EditeBulletin')) return;

    extract($args);

    // prepare for database query
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $subtable = $xartable['ebulletin_subscriptions'];

    // get subscribers
    $bindvars = array();
    $query = "SELECT xar_name, xar_email FROM $subtable ";
    if (isset($pid)) {
        $query .= " WHERE xar_pid = ?";
        $bindvars[] = $pid;
    }
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // assemble list of subscribers
    $subs = array();
    $roles = new xarRoles();
    for (; !$result->EOF; $result->MoveNext()) {
        list($name, $email) = $result->fields;

        // if subscriber is a user of this site, get from Roles
        if (is_numeric($email)) {

            // get user data
            $uid = $email;
            $user = $roles->getRole($uid);

            // only include if user is active
            if ($user->getState() != 3) continue;

            // retrieve name and email
            $email = $user->getEmail();
            $name = $user->getName();
        }
        $subs[strtolower($email)] = $name;
    }
    $result->Close();

    // success
    return $subs;
}

?>
