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
 * @subpackage xproject module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function xproject_adminapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($sendmails) || $sendmails == 0) {
        $invalid[] = 'sendmails';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'create', 'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
	
    if (!xarSecAuthAction(0, 'xproject::Project', "$name::", ACCESS_ADD)) {
        $msg = xarML('Not authorized to add #(1) items',
                    'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }
		
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xprojecttable = $xartable['xproject'];

    $nextId = $dbconn->GenId($xprojecttable);

    $sql = "INSERT INTO $xprojecttable (
              xar_projectid,
              xar_name,
			  xar_description,
			  xar_usedatefields,
			  xar_usehoursfields,
              xar_usefreqfields,
              xar_allowprivate,
              xar_importantdays,
			  xar_criticaldays, 
			  xar_sendmailfreq, 
			  xar_billable)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($name) . "',
              '" . xarVarPrepForStore($description) . "',
              " . ($displaydates ? $displaydates : "NULL") . ",
              " . ($displayhours ? $displayhours : "NULL") . ",
              " . ($displayfreq ? $displayfreq : "NULL") . ",
              " . ($private ? $private : "NULL") . ",
              " . $importantdays . ",
              " . $criticaldays . ",
              " . $sendmails . ",
              " . ($billable ? $billable : "NULL") . ")";			  

// PRIVATE INITIALLY SET BASED ON USER PREFERENCE

    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $projectid = $dbconn->PO_Insert_ID($xprojecttable, 'xar_projectid');

    $item = $args;
    $item['module'] = 'xproject';
    $item['itemid'] = $projectid;
    xarModCallHooks('item', 'create', $projectid, $item);

    return $projectid;
}

?>