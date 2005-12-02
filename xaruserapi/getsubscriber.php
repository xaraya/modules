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

    // get user id
    if (empty($uid)) $uid = xarUserGetVar('uid');

    // validate inputs
    if (!isset($uid) || !is_numeric($uid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'user ID', 'user', 'getsubscriber', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // prepare for database query
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubstable = $xartable['ebulletin'];
    $substable = $xartable['ebulletin_subscriptions'];

    // generate query
    $query = "
        SELECT
            $substable.xar_id,
            $substable.xar_pid,
            $substable.xar_name,
            $substable.xar_email,
            $pubstable.xar_name as pubname
        FROM $substable, $pubstable
        WHERE $substable.xar_pid = $pubstable.xar_id
        AND $substable.xar_email = ?
        ORDER BY $substable.xar_pid ASC ";

    // perform query
    $result = $dbconn->Execute($query, array($uid));
    if (!$result) return;

    // assemble results
    $subs = array();
    $roles = new xarRoles();

    for (; !$result->EOF; $result->MoveNext()) {

        // extract this row
        list($id, $pid, $name, $email, $pubname) = $result->fields;

        // if subscriber is a user of this site, get from Roles
        $registered = false;
        if (is_numeric($email)) {

            $registered = true;

            // get user data
            $uid = $email;
            $user = $roles->getRole($uid);

            // only include if user is active
            if ($user->getState() != 3) continue;

            // retrieve name and email
            // note: an error is thrown if we try to get email and we're not
            // logged in.  but to see subscriber list, you have to be logged
            // in anyway, unless your permissions are insane!!!
            $email = xarUserIsLoggedIn() ? $user->getEmail() : '';
            $name = $user->getName();
        }
        $subs[] = array(
            'id' => $id,
            'pid' => $pid,
            'name' => $name,
            'email' => $email,
            'pubname' => $pubname,
            'reg' => $registered
        );
    }
    $result->Close();

    // success
    return $subs;
}

?>
