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

    $regname = strtolower($regname);
    // Argument check
    if (!ereg("^[a-z0-9][a-z0-9_-]*[a-z0-9]$", $regname)) {
        $msg = xarML('Wrong symbols in registered name.');
        xarExceptionSet(XAR_USER_EXCEPTION,
                        'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // Check if that regname exists
    $query = "SELECT xar_rid FROM $releasetable
            WHERE xar_regname='".xarVarPrepForStore($regname)."'
            AND xar_type='".xarVarPrepForStore($type)."'";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    if ($result->RecordCount() > 0) {
        $msg = xarML('Sorry, requested regname/type pair already registered earlier.');
        xarExceptionSet(XAR_USER_EXCEPTION,
                        'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    if (empty($approved)){
        $approved = 1;
    }

    // Get all IDs
    $query = "SELECT xar_rid FROM $releasetable ORDER BY xar_rid";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    $rid = 0;
    for (; !$result->EOF; $result->MoveNext()) {
        $nextid = $result->fields[0];
        $rid++;
        if ($rid == $nextid) continue;
        break;
    }
    $result->Close();

    if ($rid == 0) return;

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