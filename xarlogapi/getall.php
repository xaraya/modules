<?php
/**
 *
 *
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage accessmethods module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function accessmethods_logapi_getall($args)
{
    extract($args);

    $invalid = array();
    if (!isset($siteid) || !is_numeric($siteid)) {
        $invalid[] = 'siteid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'log', 'getall', 'xProject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarSecurityCheck('EditXProject', 0, 'Item', "All:All:All")) {//TODO: security
        /* return an empty set if not authorized; do not hard-fail
        $msg = xarML('Not authorized to access #(1) items',
                    'accessmethods');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
        */
        return array();
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $accessmethodstable = $xartable['accessmethods'];
    $logtable = $xartable['accessmethods_log'];

    $sql = "SELECT $logtable.logid,
                  $logtable.changetype,
                  $logtable.siteid,
                  $accessmethodstable.site_name,
                  $logtable.details,
                  $logtable.createdate,
                  $logtable.userid
            FROM $logtable, $accessmethodstable
            WHERE $accessmethodstable.siteid = $logtable.siteid
            AND $logtable.siteid = $siteid";

//    $sql .= " WHERE $taskcolumn[parentid] = $parentid";
//    $sql .= " AND $taskcolumn[siteid] = $siteid";
//    if($groupid > 0) $sql .= " AND $taskcolumn[groupid] = $groupid";
    $sql .= " ORDER BY $logtable.createdate DESC";

/*
    if ($selected_project != "all") {
        $sql .= " AND $accessmethods_todos_column[project_id]=".$selected_project;

    if (xarSessionGetVar('accessmethods_my_tasks') == 1 ) {
        // show only tasks where I'm responsible for
        $query .= "
            AND $accessmethods_responsible_persons_column[user_id] = ".xarUserGetVar('uid')."
            AND $accessmethods_todos_column[todo_id] = $accessmethods_responsible_persons_column[todo_id]";
    }

    // WHERE CLAUSE TO NOT PULL IF TASK IS PRIVATE AND USER IS NOT OWNER, CREATOR, ASSIGNER, OR ADMIN
    // CLAUSE TO FILTER BY STATUS, MIN PRIORITY, OR DATES
    // CLAUSE WHERE USER IS OWNER
    // CLAUSE WHERE USER IS CREATOR
    // CLAUSE WHERE USER IS ASSIGNER
    // CLAUSE FOR ACTIVE ONLY (ie. started but not yet completed)
    // CLAUSE BY TEAM/GROUPID (always on?)
    //
    // CLAUSE TO PULL PARENT TASK SETS
    // or
    // USERAPI_GET FOR EACH PARENT LEVEL
*/

    $result = $dbconn->Execute($sql);

    if (!$result) return;

    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($logid,
              $changetype,
              $siteid,
              $site_name,
              $details,
              $createdate,
              $userid) = $result->fields;
        if (xarSecurityCheck('EditAccessMethods', 0, 'All', "$site_name:All:$siteid")) {
            $items[] = array('logid'                => $logid,
                              'changetype'          => $changetype,
                              'siteid'           => $siteid,
                              'site_name'        => $site_name,
                              'details'             => $details,
                              'createdate'          => $createdate == "0000-00-00" ? NULL : $createdate,
                              'userid'              => $userid);
        }
    }

    $result->Close();

    return $items;
}

?>