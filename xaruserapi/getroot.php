<?php
/**
 * Get the root task
 *
 */
function tasks_userapi_getroot($args)
{
    extract($args);
    
    if (!isset($id) || !is_numeric($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'ID', 'user', 'getroot', 'events');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($msg));
        return false;
    } 

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['tasks'];
    $taskscolumn = &$xartable['tasks_columns'];
    $rootid = $id;
    $parentid = $id;
    while($rootid != 0) {
        $sql = "SELECT $taskscolumn[id],
                    $taskscolumn[parentid]
                FROM $taskstable
                WHERE xar_id = $rootid";
        $result =& $dbconn->Execute($sql);
        if (!$result) return;

        list($parentid, $rootid) = $result->fields;
        $result->Close();
    }

    return $parentid;
}

?>