<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_adminapi_migrate($args)
{
    extract($args);

    $invalid = array();
    if (!isset($targetfocus)) {
        $targetfocus = 0;
    }

    $item = xarModAPIFunc('xproject',
                        'user',
                        'get',
                        array('projectid' => $projectid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    if (!xarSecAuthAction(0, 'xproject::Project', "$item[name]::$projectid", ACCESS_MODERATE)) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['xproject_tasks'];
    if(is_array($taskfocus)) {
        foreach($taskfocus as $targetid => $focus) {
            if($focus) $targetfocus = $targetid;
        }
    }

    $affectedtasks = array();
    if(is_array($taskcheck)) {
        foreach($taskcheck as $affectedid => $check) {
            if($affectedid != $targetfocus) $affectedtasks[] = $affectedid;
        }
    }

    if($targetfocus > 0) {
        // WTF WE'RE GONNA TRY TO DO HERE:
        //
        // - CASE OUT EACH OF FOUR POSSIBLE REASONS WE'RE HERE
        // - 1 => Migrate selected tasks under taskfocus (taskfocus[any] = 1)
        $implode_affected = implode(",",$affectedtasks);
        echo $implode_affected;
        $query = "UPDATE $taskstable SET xar_parentid = $targetfocus WHERE xar_taskid IN ($implode_affected)";

        $result = &$dbconn->Execute($query);

        if (!$result) return;

        return $targetfocus;

    } elseif($taskoption == 1) {
        // - 2 => Surface selected tasks to current task's parentid (taskoption = 1)
        // UH, THERE IS NO PARENTID PASSED
        $sql = "UPDATE $taskstable SET xar_parentid = " . ($item['parentid'] ? $item['parentid'] : "0") . " WHERE xar_taskid IN (" . implode(",",$affectedtasks) . ")";

        $dbconn->Execute($sql);

        /* Check for an error with the database code, adodb has already raised
         * the exception so we just return
         */
        if (!$result) return;

        return $taskid;

    } elseif($taskoption == 2) {
        // - 3 => Delete task and all subtasks (taskoption = 2)
        $resultlist = array();
        $resultlist[] = $affectedtasks;
        $selectedids = $affectedtasks;
        $numtasks = count($affectedtasks);
        while($numtasks > 0) {
            $sql = "SELECT xar_taskid FROM $taskstable WHERE xar_parentid IN (" . implode(",",$selectedids) . ")";

            $result = $dbconn->SelectLimit($sql, -1, 0);

            /* Check for an error with the database code, adodb has already raised
             * the exception so we just return
             */
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
            $sql = "DELETE FROM $taskstable WHERE xar_taskid IN (" . implode(",",$tasklist) . ")";

            $dbconn->Execute($sql);

            /* Check for an error with the database code, adodb has already raised
             * the exception so we just return
             */
            if (!$result) return;
        }

        return $taskid;

    } elseif($taskoption == 3) {
        // - 4 => Delete task, but surface children under current task
        // WHICH SHOULD GO FIRST?
        // ? IF UPDATE FAILS FIRST, ERRMSG AND DO NOT DEL
        // ? IF DEL FAILS FIRST, CONTINUE W/UPDATE
        // IN SECOND SCENARIO, UNSUCCESSFUL UPDATES BECOME ORPHANS
        // HANDLE THAT AS PREVIOUSLY NOTED
        $sql = "UPDATE $taskstable SET xar_parentid = $taskid WHERE xar_parentid IN (" . implode(",",$affectedtasks) . ")";

        $dbconn->Execute($sql);

        /* Check for an error with the database code, adodb has already raised
         * the exception so we just return
         */
        if (!$result) return;

        $sql = "DELETE FROM $taskstable WHERE xar_taskid IN (" . implode(",",$affectedtasks) . ")";

        $dbconn->Execute($sql);

        /* Check for an error with the database code, adodb has already raised
         * the exception so we just return
         */
        if (!$result) return;

        return $taskid;

    } else {
        $sql = "(no query)";
    }
    //
    // EXPECTED ISSUES:
    // * Deletion of subtasks must be recursive/iterative
    // (resolved by creating an array*array of taskid lists to use with an "IN" statement recursively)
    // everything else looks pretty cake, yeah?
    //
    ///////////////////////////////////

    return $taskid;
}

?>