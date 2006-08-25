<?php
 
function xproject_adminapi_reprioritize($args)
{
    extract($args);

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'project ID';
    }
    if (!isset($mode) || !is_string($mode)) {
        $invalid[] = 'mode';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('xproject',
                            'user',
                            'get',
                            array('projectid' => $projectid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$item[project_name]:All:$projectid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $project_table = $xartable['xProjects'];

    if($mode == "incr") {
        $query = "UPDATE $project_table
                SET priority = priority + 1
                WHERE projectid = ?";
        $priority = $item['priority'] + 1;
    } else {
        $query = "UPDATE $project_table
                SET priority = priority - 1
                WHERE projectid = ?";
        $priority = $item['priority'] - 1;
    }
    
    $bindvars = array($projectid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    $userid = xarUserGetVar('uid');
    $logdetails = "Project modified.";
    $logdetails .= "<br>Project priority changed from ".$item['priority']." to ".$priority;
        
    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $projectid,
                            'userid'        => $userid,
                            'details'	    => $logdetails,
                            'changetype'	=> "MODIFIED"));

    return true;
}
?>