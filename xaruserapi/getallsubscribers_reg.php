<?php
/**
* Get all registered subscribers
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
* get all registered subscribers
*
* pid optional
* state optional (registered vs not)
* ids optional
* emails optional
*/
function ebulletin_userapi_getallsubscribers_reg($args)
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
    if (empty($pid)) $pid = '';
    if (empty($uids)) $uids = array();
    if (empty($filter)) $filter = array();

    // validate vars
    $invalid = array();
    if (empty($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (empty($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (isset($order) && !in_array($order, array('name', 'pubname', 'email'))) {
        $invalid[] = 'order';
    }
    if (isset($sort) && ($sort != 'ASC' && $sort != 'DESC')) {
        $invalid[] = 'sort';
    }
    if (isset($ids) && !is_array($ids)) {
        $invalid[] = 'subscriber IDs';
    }
    if (!empty($pid) && !is_numeric($pid)) {
        $invalid[] = 'publication ID';
    }
    if (isset($uids) && !is_array($uids)) {
        $invalid[] = 'uids';
    }
    if (isset($filter) && !is_array($filter)) {
        $invalid[] = 'filter';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'userapi', 'getallsubscribers_reg', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // prepare for database query
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubstable = $xartable['ebulletin'];
    $substable = $xartable['ebulletin_subscriptions'];
    $rolestable = $xartable['roles'];

    // generate query
    $query = "
        SELECT
            $substable.xar_id,
            $substable.xar_pid,
            $rolestable.xar_name,
            $rolestable.xar_email,
            $pubstable.xar_name as pubname,
            $substable.xar_uid
        FROM $substable, $pubstable, $rolestable
        WHERE $substable.xar_pid = $pubstable.xar_id
        AND $substable.xar_uid = $rolestable.xar_uid
        AND $rolestable.xar_state = ?
    ";
    $bindvars = array(3);
    if ($filter) {
        switch($filter['type']) {
        case 'starts':
                $test_pre = '';
                $test_post = '%';
            break;
        case 'ends':
                $test_pre = '%';
                $test_post = '';
            break;
        case 'equals':
                $test_pre = '';
                $test_post = '';
            break;
        case 'contains':
        default:
                $test_pre = '%';
                $test_post = '%';
        }
        switch($filter['col']) {
        case 'pubname':
            $query .= "AND $pubstable.xar_name LIKE ?\n";
            $bindvars[] = "$test_pre$filter[text]$test_post";
            break;
        case 'email':
            $query .= "AND $rolestable.xar_email LIKE ?\n";
            $bindvars[] = "$test_pre$filter[text]$test_post";
            break;
        case 'name':
        default:
            $query .= "AND $rolestable.xar_name LIKE ?\n";
            $bindvars[] = "$test_pre$filter[text]$test_post";
        }
    }
    if ($pid) {
        $query .= "AND $substable.xar_pid = ?\n";
        $bindvars[] = $pid;
    }
    if ($ids) {
        $query_ors = array();
        foreach ($ids as $id) {
            $query_ors[] = "$substable.xar_id = ?";
            $bindvars[] = $id;
        }
        $query .= "AND (".join(" OR\n", $query_ors).")\n";
    } elseif ($uids) {
        $query_ors = array();
        foreach ($uids as $uid) {
            $query_ors[] = "$substable.xar_uid = ?";
            $bindvars[] = $uid;
        }
        $query .= "AND (".join(" OR\n", $query_ors).")\n";
    }
    switch($order) {
        case 'pubname':
            $query .= "ORDER BY $pubstable.xar_name $sort\n";
            break;
        case 'email':
            $query .= "ORDER BY $rolestable.xar_email $sort\n";
            break;
        case 'name':
        default:
            $query .= "ORDER BY $rolestable.xar_name $sort\n";
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
        list($id, $pid, $name, $email, $pubname, $uid) = $result->fields;

        $subs[] = array(
            'id' => $id,
            'pid' => $pid,
            'name' => $name,
            'email' => $email,
            'pubname' => $pubname,
            'uid' => $uid
        );
    }
    $result->Close();

    // success
    return $subs;
}

?>
