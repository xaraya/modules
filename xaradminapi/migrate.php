<?php
/**
 * Migrate a task
 *
 */
function tasks_adminapi_migrate($args)
{
    extract($args);

    if (!isset($targetfocus)) $targetfocus = 0;

    if(is_array($taskfocus) && count($taskfocus) > 0) {
        foreach($taskfocus as $targetid => $focus) {
            if($focus) $targetfocus = $targetid;
        }
    }

    $affectedtasks = array();
    if(is_array($taskcheck) && count($taskcheck) > 0) {
        foreach($taskcheck as $affectedid => $check) {
            if($affectedid != $targetfocus) $affectedtasks[] = $affectedid;
        }
    }

    if(count($affectedtasks) <= 0) {
        return false;
    }

// NEED TO ADAPT TO ROOT TASK PERMISSIONS USING GETROOT AND GET
    if(empty($parentid)) {
        $id = ($targetfocus > 0 ? $targetfocus : $affectedid);
    } else $id = $parentid;
// echo "id: $id, parentid: $parentid, targetfocus: $targetfocus, affectedid: $affectedid<br>";
    $parenttask = xarModAPIFunc('tasks',
                            'user',
                            'get',
                            array('id' => $id));

    if ($parenttask == false) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . xarML("No such item"));
        return false;
    }

//     if (!xarSecAuthAction(0, 'tasks::task', '::$parenttask[basetaskid]', ACCESS_MODERATE)) {
//         xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_migrate: ' . $parenttask['basetaskid'] .  _TASKS_NOAUTH);
//         return false;
//     }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['tasks'];

    if($targetfocus > 0) {
        // - 1 => Migrate selected tasks under taskfocus (taskfocus[any] = 1)
        $sql = "UPDATE $taskstable SET xar_parentid = " . ($targetfocus ? $targetfocus : "0") . " WHERE xar_id IN (" . implode(",",$affectedtasks) . ")";

        $res =& $dbconn->Execute($sql);
        if (!$res) return;

        $returnid = (xarModGetVar('tasks','returnfrommigrate') ? $targetfocus : $parentid);
        return $returnid;

    } elseif($taskoption == 1) {
        // - 2 => Surface selected tasks to current task's parentid (taskoption = 1)
        // UH, THERE IS NO PARENTID PASSED
        $sql = "UPDATE $taskstable SET xar_parentid = " . ($parenttask['parentid'] ? $parenttask['parentid'] : "0") . " WHERE xar_id IN (" . implode(",",$affectedtasks) . ")";

        $res =& $dbconn->Execute($sql);
        if (!$res) return;

        $returnid = (xarModGetVar('tasks','returnfromsurface') ? $parentid : $parenttask['parentid']);
        return $returnid;

    } elseif($taskoption == 2) {
        // - 3 => Delete selected tasks and all subtasks (taskoption = 2)
        $resultlist = array();
        $resultlist[] = $affectedtasks;
        $selectedids = $affectedtasks;
        $numtasks = count($affectedtasks);
        while($numtasks > 0) {
            $sql = "SELECT xar_id FROM $taskstable WHERE xar_parentid IN (" . implode(",",$selectedids) . ")";

            $result = $dbconn->SelectLimit($sql, -1, 0);
            if (!$result) return;

            $selectedids = array();
            for (; !$result->EOF; $result->MoveNext()) {
                list($selectedid) = $result->fields;
                $selectedids[] = $selectedid;
            }
            $resultlist[] = $selectedids;
            $numtasks = count($selectedids);
        }

        foreach($resultlist as $tasklist) {
            if(is_array($tasklist) && count($tasklist) > 0) {
                $sql = "DELETE FROM $taskstable WHERE xar_id IN (" . implode(",",$tasklist) . ")";

                $res =& $dbconn->Execute($sql);
                if (!$res) return;

            }
        }

        return $parentid;

    } elseif($taskoption == 3) {
        // - 4 => Delete task, but surface children under current task
        // WHICH SHOULD GO FIRST?
        // ? IF UPDATE FAILS FIRST, ERRMSG AND DO NOT DEL
        // ? IF DEL FAILS FIRST, CONTINUE W/UPDATE
        // IN SECOND SCENARIO, UNSUCCESSFUL UPDATES BECOME ORPHANS
        // HANDLE THAT AS PREVIOUSLY NOTED
        $sql = "UPDATE $taskstable SET xar_parentid = " . ($parentid ? $parentid : "0") . " WHERE xar_parentid IN (" . implode(",",$affectedtasks) . ")";

        $res =& $dbconn->Execute($sql);
        if (!$res) return;

        $sql = "DELETE FROM $taskstable WHERE xar_id IN (" . implode(",",$affectedtasks) . ")";

        $res =& $dbconn->Execute($sql);
        if (!$res) return;

        return $parentid;

    } else $sql = "(no query)";
    //
    // EXPECTED ISSUES:
    // * Deletion of subtasks must be recursive/iterative
    // (resolved by creating an array*array of id lists to use with an "IN" statement recursively)
    // everything else looks pretty cake, yeah?
    //
    ///////////////////////////////////

    return $parentid;
}

?>