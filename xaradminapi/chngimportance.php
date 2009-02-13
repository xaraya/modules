<?php
 
function xtasks_adminapi_chngimportance($args)
{
    extract($args);

    $invalid = array();
    if (!isset($taskid) || !is_numeric($taskid)) {
        $invalid[] = 'Task ID';
    }
    if (!isset($mode) || !is_string($mode)) {
        $invalid[] = 'mode';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('xtasks',
                            'user',
                            'get',
                            array('taskid' => $taskid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditXTask', 1, 'Item', "$item[task_name]:All:$taskid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xtasktable = $xartable['xtasks'];

    if($mode == "incr") {
        $query = "UPDATE $xtasktable
                SET importance = importance + 2
                WHERE taskid = ?";
    } else {
        $query = "UPDATE $xtasktable
                SET importance = importance - 2
                WHERE taskid = ?";
    }
    
    $bindvars = array($taskid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;
    
    $mymemberid = xarModGetUserVar('xproject', 'mymemberid');
    if(!empty($item['owner']) && $item['owner'] != $mymemberid) {
        xarModAPIFunc('xtasks', 'user', 'notify', array('owner' => $item['owner'], 'taskid' => $taskid, 'action' => "IMPORTANCE"));
    }

    return true;
}
?>