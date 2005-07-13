<?php

function xproject_userapi_get($args)
{
    extract($args);

    if (!isset($projectid) || !is_numeric($projectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'user', 'get', 'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xprojecttable = $xartable['xproject'];

    $sql = "SELECT xar_projectid,
                   xar_name,
                   xar_description,
                   xar_usedatefields,
				   xar_usehoursfields,
				   xar_usefreqfields,
				   xar_allowprivate,
				   xar_importantdays,
				   xar_criticaldays,
				   xar_sendmailfreq,
				   xar_billable
			FROM $xprojecttable
            WHERE xar_projectid = " . $projectid;
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

	list($projectid,
		$name, 
		$description, 
		$usedatefields, 
		$usehoursfields, 
		$usefreqfields, 
		$allowprivate, 
		$importantdays, 
		$criticaldays, 
		$sendmailfreq, 
		$billable) = $result->fields;
		
    $result->Close();

    if (!xarSecAuthAction(0, 'xproject::', "$name::$projectid", ACCESS_READ)) {
        $msg = xarML('Not authorized to access #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $task = array('projectid' => $projectid,
				 'name' => $name,
				 'description' => $description,
				 'usedatefields' => $usedatefields,
				 'usehoursfields' => $usehoursfields,
				 'usefreqfields' => $usefreqfields,
				 'allowprivate' => $allowprivate,
				 'importantdays' => $importantdays,
				 'criticaldays' => $criticaldays,
				 'sendmailfreq' => $sendmailfreq,
				 'billable' => $billable);

    return $task;
}

?>