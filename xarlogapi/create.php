<?php
/**
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage accessmethods module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function accessmethods_logapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($siteid) || !is_numeric($siteid)) {
        $invalid[] = 'siteid';
    }
    if (!isset($changetype) || !is_string($changetype)) {
        $invalid[] = 'changetype';
    }
    if (!isset($details) || !is_string($details)) {
        $invalid[] = 'details';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'log', 'create', 'accessmethods');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('EditAccessMethods', 1, 'All', "All:All:All")) {
        $msg = xarML('Not authorized to add #(1) items to #(2) module',
                    'log', 'accessmethods');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $logtable = $xartable['accessmethods_log'];

    $nextId = $dbconn->GenId($logtable);

    $userid = xarUserGetVar('uid');
    $query = "INSERT INTO $logtable (
                  logid,
                  changetype,
                  siteid,
                  userid,
                  details,
                  createdate)
            VALUES (?,?,?,?,?, NOW() )";

    $bindvars = array(
              $nextId,
              $changetype,
              $siteid,
              $userid,
              $details);

    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $logid = $dbconn->PO_Insert_ID($logtable, 'logid');

    return $logid;
}

?>