<?php

function xproject_adminapi_delete($args)
{
    extract($args);

    if (!isset($projectid) || !is_numeric($projectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'admin', 'delete', 'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // does it exist ?
    $project = xarModAPIFunc('xproject',
							'user',
							'get',
							array('projectid' => $projectid));

    if (!isset($project) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    if (!xarSecAuthAction(0, 'xproject::Project', "$project[name]::$projectid", ACCESS_DELETE)) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xprojecttable = $xartable['xproject'];

    // does it have children ?
    $sql = "DELETE FROM $xprojecttable
            WHERE xar_projectid = " . xarVarPrepForStore($projectid);
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item['module'] = 'xproject';
    $item['itemid'] = $projectid;
    xarModCallHooks('item', 'delete', $projectid, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>