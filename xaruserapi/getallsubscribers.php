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
* state optional (registered vs not)
* ids optional
* emails optional
*/
function ebulletin_userapi_getallsubscribers($args)
{
    // security check
    if (!xarSecurityCheck('EditeBulletin')) return;

    extract($args);

    // set defaults
    if (empty($startnum)) $startnum = 1;
    if (empty($numitems)) $numitems = -1;
    if (empty($order)) $order = 'name';
    if (empty($sort)) $sort = 'ASC';
    if (empty($ids)) $ids = array();
    if (empty($emails)) $emails = array();

    // validate vars
    $invalid = array();
    if (empty($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (empty($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (isset($order) && !in_array($order, array('id', 'pid', 'name', 'email'))) {
        $invalid[] = 'order';
    }
    if (isset($sort) && ($sort != 'ASC' && $sort != 'DESC')) {
        $invalid[] = 'sort';
    }
    if (isset($ids) && !is_array($ids)) {
        $invalid[] = 'subscriber IDs';
    }
    if (isset($emails) && !is_array($emails)) {
        $invalid[] = 'emails';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'userapi', 'getallsubscribers', 'eBulletin');
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
        WHERE $substable.xar_pid = $pubstable.xar_id ";
    $bindvars = array();
    if ($ids) {
        $query_ors = array();
        foreach ($ids as $id) {
            $query_ors[] = "$substable.xar_id = ?";
            $bindvars[] = $id;
        }
        $query .= 'AND ('.join(" OR\n", $query_ors).')';
    } elseif ($emails) {
        $query_ors = array();
        foreach ($emails as $email) {
            $query_ors[] = "$substable.xar_email = ?";
            $bindvars[] = $email;
        }
        $query .= 'AND ('.join(" OR\n", $query_ors).')';
    }
    switch($order) {
        case 'pub': case 'pid': case 'pubname':
            $query .= "ORDER BY $substable.xar_pid $sort, $substable.xar_name ASC ";
            break;
        case 'email':
            $query .= "ORDER BY $substable.xar_email $sort ";
            break;
        case 'name': default:
            $query .= "ORDER BY $substable.xar_name $sort, $substable.xar_pid ASC ";
            break;
    }

    // perform query
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
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
            $email = $user->getEmail();
            $name = $user->getName();
        }
        $subs[] = array(
            'id' => $id,
            'pid' => $pid,
            'name' => $name,
            'email' => $email,
            'pubname' => $pubname,
            'registered' => $registered,
            'uid' => ($registered) ? $uid : NULL
        );
    }
    $result->Close();

    // success
    return $subs;
}

?>
