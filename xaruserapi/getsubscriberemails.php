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
    // security check REMOVED to allow scheduler to work anonymously
    //    if (!xarSecurityCheck('EditeBulletin')) return;

    extract($args);

    // prepare for database query
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $substable = $xartable['ebulletin_subscriptions'];
    $rolestable = $xartable['roles'];

    // get subscribers
    $bindvars = array();
    $query = "
        SELECT
            $substable.xar_name,
            $substable.xar_email,
            $substable.xar_uid,
            $rolestable.xar_name AS xar_rolename,
            $rolestable.xar_email AS xar_roleemail
, $substable.xar_pid
        FROM $substable
        LEFT JOIN $rolestable
            ON $substable.xar_uid = $rolestable.xar_uid
        WHERE 1
        AND (
            $rolestable.xar_state IS NULL
            OR $rolestable.xar_state = ?
        )
    ";
    $bindvars[] = 3;

    if (isset($pid)) {
        $query .= "AND $substable.xar_pid = ?\n";
        $bindvars[] = $pid;
    }
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // assemble list of subscribers
    $subs = array();
    $roles = new xarRoles();
    for (; !$result->EOF; $result->MoveNext()) {
        list($name, $email, $uid, $rolename, $roleemail) = $result->fields;

        // account for subscribed users
        if (!empty($uid)) {
            $email = $roleemail;
            $name = $rolename;
        }

        $subs[strtolower($email)] = $name;
    }
    $result->Close();

    // success
    return $subs;
}

?>
