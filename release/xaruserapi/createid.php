<?php

function release_userapi_createid($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if ((!isset($name)) ||
        (!isset($type))) {

        $msg = xarML('Wrong arguments to release_userapi_create.');
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
            WHERE xar_name='".xarVarPrepForStore($name)."';";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    if ($result->RecordCount() > 0) {
        return; 
    }

    if (empty($approved)){
        $approved = 1;
    }

    $query = "INSERT INTO $releasetable (
              xar_rid,
              xar_uid,
              xar_name,
              xar_desc,
              xar_type,
              xar_certified,
              xar_approved
              )
            VALUES (
              '" . xarVarPrepForStore($rid) . "',
              '" . xarVarPrepForStore($uid) . "',
              '" . xarVarPrepForStore($name) . "',
              '" . xarVarPrepForStore($desc) . "',
              '" . xarVarPrepForStore($type) . "',
              '" . xarVarPrepForStore($certified) . "',
              '" . xarVarPrepForStore($approved) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'create', $rid, 'rid');

    // Return the id of the newly created user to the calling process
    return $rid;

}

?>