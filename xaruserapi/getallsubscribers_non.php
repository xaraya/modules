<?php
/**
* Get all non-registered subscribers
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
* get all non-registered subscribers
*
* pid optional
* state optional (registered vs not)
* ids optional
* emails optional
*/
function ebulletin_userapi_getallsubscribers_non($args)
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
    if (empty($emails)) $emails = array();
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
    if (isset($emails) && !is_array($emails)) {
        $invalid[] = 'emails';
    }
    if (isset($filter) && !is_array($filter)) {
        $invalid[] = 'filter';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'userapi', 'getallsubscribers_non', 'eBulletin');
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
        AND $substable.xar_uid = ?
    ";
    $bindvars = array('');
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
            $query .= "AND $substable.xar_email LIKE ?\n";
            $bindvars[] = "$test_pre$filter[text]$test_post";
            break;
        case 'name':
        default:
            $query .= "AND $substable.xar_name LIKE ?\n";
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
    } elseif ($emails) {
        $query_ors = array();
        foreach ($emails as $email) {
            $query_ors[] = "$substable.xar_email = ?";
            $bindvars[] = $email;
        }
        $query .= "AND (".join(" OR\n", $query_ors).")\n";
    }
    switch($order) {
        case 'pub': case 'pid': case 'pubname':
            $query .= "ORDER BY $substable.xar_pid $sort, $substable.xar_name ASC\n";
            break;
        case 'email':
            $query .= "ORDER BY $substable.xar_email $sort\n";
            break;
        case 'name': default:
            $query .= "ORDER BY $substable.xar_name $sort, $substable.xar_pid ASC\n";
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

        $subs[] = array(
            'id' => $id,
            'pid' => $pid,
            'name' => $name,
            'email' => $email,
            'pubname' => $pubname
        );
    }
    $result->Close();

    // success
    return $subs;
}

?>
