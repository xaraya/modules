<?php
/**
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xproject module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function xproject_logapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'projectid';
    }
    if (!isset($changetype) || !is_string($changetype)) {
        $invalid[] = 'changetype';
    }
    if (!isset($details) || !is_string($details)) {
        $invalid[] = 'details';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'create', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "All:All:All")) {
        $msg = xarML('Not authorized to add #(1) items to #(2) module',
                    'log', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $logtable = $xartable['xProject_log'];

    $nextId = $dbconn->GenId($logtable);

    $userid = xarUserGetVar('uid');
    $query = "INSERT INTO $logtable (
                  logid,
                  changetype,
                  projectid,
                  userid,
                  details,
                  createdate)
            VALUES (?,?,?,?,?, NOW() )";

    $bindvars = array(
              $nextId,
              $changetype,
              $projectid,
              $userid,
              $details);

    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $logid = $dbconn->PO_Insert_ID($logtable, 'logid');

    return $logid;
}

?>