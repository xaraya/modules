<?php

function xproject_adminapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'item ID';
    }
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($sendmailfreq) || $sendmailfreq == 0) {
        $invalid[] = 'sendmails';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'update', 'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('xproject',
						'user',
						'get',
						array('projectid' => $projectid));
			
	if (!isset($item) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    if (!xarSecAuthAction(0, 'xproject::Project', "$item[name]::$projectid", ACCESS_EDIT)) {
        $msg = xarML('Not authorized to edit #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }
    if (!xarSecAuthAction(0, 'xproject::Project', "$name::$projectid", ACCESS_EDIT)) {
        $msg = xarML('Not authorized to edit #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }
		
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xprojecttable = $xartable['xproject'];

    $sql = "UPDATE $xprojecttable
              SET xar_name = '" . xarVarPrepForStore($name) . "',
				  xar_description = '" . xarVarPrepForStore($description) . "',
				  xar_usedatefields = " . ($displaydates ? $displaydates : "NULL") . ",
				  xar_usehoursfields = " . ($displayhours ? $displayhours : "NULL") . ",
				  xar_usefreqfields = " . ($displayfreq ? $displayfreq : "NULL") . ",
				  xar_allowprivate = " . ($private ? $private : "NULL") . ",
				  xar_importantdays = " . $importantdays . ",
				  xar_criticaldays = " . $criticaldays . ", 
				  xar_sendmailfreq = " . $sendmailfreq . ", 
				  xar_billable = " . ($billable ? $billable : "NULL") . "
			WHERE xar_projectid = $projectid";

    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item['module'] = 'xproject';
    $item['itemid'] = $projectid;
    $item['name'] = $name;
    $item['description'] = $description;
    xarModCallHooks('item', 'update', $projectid, $item);

    return true;
}

?>