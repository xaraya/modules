<?php

function release_userapi_createid($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if ((!isset($regname)) ||
        (!isset($type))) {

        $msg = xarML('Wrong arguments to release_userapi_createid.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,
                        'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // Check if that username exists
    $query = "SELECT xar_rid FROM $releasetable
            WHERE xar_regname='".xarVarPrepForStore($regname)."'
            AND xar_type='".xarVarPrepForStore($type)."'
            OR xar_rid='".xarVarPrepForStore($rid)."'";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    if ($result->RecordCount() > 0) {
        $msg = xarML('Requested ID or name/type already registered earlier.');
        xarExceptionSet(XAR_USER_EXCEPTION,
                        'MISSING_DATA',
                        new SystemException($msg));
        return false;
    }

    if (empty($approved)){
        $approved = 1;
    }

    $query = "INSERT INTO $releasetable (
              xar_rid,
              xar_uid,
              xar_regname,
              xar_displname,
              xar_desc,
              xar_type,
              xar_class,
              xar_certified,
              xar_approved,
              xar_rstate
              )
            VALUES (
              '" . xarVarPrepForStore($rid) . "',
              '" . xarVarPrepForStore($uid) . "',
              '" . xarVarPrepForStore($regname) . "',
              '" . xarVarPrepForStore($displname) . "',
              '" . xarVarPrepForStore($desc) . "',
              '" . xarVarPrepForStore($type) . "',
              '" . xarVarPrepForStore($class) . "',
              '" . xarVarPrepForStore($certified) . "',
              '" . xarVarPrepForStore($approved) . "',
              '" . xarVarPrepForStore($rstate)."')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'create', $rid, 'rid');

    // Return the id of the newly created user to the calling process
    return $rid;

}

?>