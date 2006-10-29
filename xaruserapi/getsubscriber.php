<?php
/**
* Get a subscriber
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
* get a subscriber
*
* uid
*/
function ebulletin_userapi_getsubscriber($args)
{
    // security check
    if (!xarSecurityCheck('ReadeBulletin')) return;

    extract($args);

    /**
    * if we have nothing, return nothing (garbage in, garbage out)
    * otherwise, do what we can
    */

    // no email or uid, we're sunk
    if (empty($uid) && empty($email)) return array();

    // prepare for database query
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubstable = $xartable['ebulletin'];
    $substable = $xartable['ebulletin_subscriptions'];
    $rolestable = $xartable['roles'];

    // generate query
    $bindvars = array();
    $query = "
        SELECT
            $substable.xar_id,
            $substable.xar_pid,
            $substable.xar_name,
            $substable.xar_email,
            $pubstable.xar_name AS xar_pubname,
            $substable.xar_uid,
            $rolestable.xar_name AS xar_rolename,
            $rolestable.xar_email AS xar_roleemail
        FROM $substable
        LEFT JOIN $pubstable ON $substable.xar_pid = $pubstable.xar_id
        LEFT JOIN $rolestable ON $substable.xar_uid = $rolestable.xar_uid
        WHERE ($rolestable.xar_state IS NULL OR $rolestable.xar_state = ?)
    ";
    $bindvars[] = 3;

    if (!empty($uid)) {
        $query .= " AND $substable.xar_uid = ?";
        $bindvars[] = $uid;
    }
    if (empty($uid) && !empty($email)) {
        $query .= " AND $substable.xar_email LIKE ?";
        $bindvars[] = $email;
    }
    $query .= " ORDER BY $substable.xar_pid ASC";

    // perform query
    $result = $dbconn->Execute($query, $bindvars);

    if (!$result) return;

    // assemble results
    $subs = array();
    $roles = new xarRoles();

    for (; !$result->EOF; $result->MoveNext()) {

        // extract this row
        list($id, $pid, $name, $email, $pubname, $uid, $rolename, $roleemail) = $result->fields;

        if (!empty($uid)) {
            $name = $rolename;
            $email = $roleemail;
        }

        $subs[] = array(
            'id'      => $id,
            'pid'     => $pid,
            'name'    => $name,
            'email'   => $email,
            'pubname' => $pubname,
            'uid'     => $uid
        );
    }
    $result->Close();

    // success
    return $subs;
}

?>
