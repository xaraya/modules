<?php

function release_userapi_createdoc($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if ((!isset($rid)) ||
        (!isset($title)) ||
        (!isset($doc)) ||
        (!isset($type)) ||
        (!isset($approved))) {

        $msg = xarML('Wrong arguments to release_userapi_createdoc.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,
                        'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_docs'];

    if (empty($approved)){
        $approved = 1;
    }

    // Get next ID in table
    $nextId = $dbconn->GenId($releasetable);
    $time = date('Y-m-d G:i:s');
    $query = "INSERT INTO $releasetable (
              xar_rdid,
              xar_rid,
              xar_title,
              xar_docs,
              xar_type,
              xar_time,
              xar_approved
              )
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($rid) . "',
              '" . xarVarPrepForStore($title) . "',
              '" . xarVarPrepForStore($doc) . "',
              '" . xarVarPrepForStore($type) . "',
              '$time',
              '" . xarVarPrepForStore($approved) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $rdid = $dbconn->PO_Insert_ID($releasetable, 'xar_rdid');

    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'create', $rdid, 'rdid');

    // Return the id of the newly created user to the calling process
    return $rdid;

}

?>