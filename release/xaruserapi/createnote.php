<?php

function release_userapi_createnote($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if ((!isset($rid)) ||
        (!isset($version))) {

        $msg = xarML('Wrong arguments to release_userapi_create.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,
                        'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_notes'];

    if (empty($approved)){
        $approved = 1;
    }

    if (empty($certified)){
        $certified = 1;
    }

    // Get next ID in table
    $nextId = $dbconn->GenId($releasetable);
    $time = date('Y-m-d G:i:s');
    $query = "INSERT INTO $releasetable (
                     xar_rnid,
                     xar_rid,
                     xar_version,
                     xar_price,
                     xar_priceterms,
                     xar_demo,
                     xar_demolink,
                     xar_dllink,
                     xar_supported,
                     xar_supportlink,
                     xar_changelog,
                     xar_notes,
                     xar_time,
                     xar_certified,
                     xar_approved,
                     xar_type
              )
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($rid) . "',
              '" . xarVarPrepForStore($version) . "',
              '" . xarVarPrepForStore($price) . "',
              '" . xarVarPrepForStore($priceterms) . "',
              '" . xarVarPrepForStore($demo) . "',
              '" . xarVarPrepForStore($demolink) . "',
              '" . xarVarPrepForStore($dllink) . "',
              '" . xarVarPrepForStore($supported) . "',
              '" . xarVarPrepForStore($supportlink) . "',
              '" . xarVarPrepForStore($changelog) . "',
              '" . xarVarPrepForStore($notes) . "',
              '$time',
              '" . xarVarPrepForStore($certified) . "',
              '" . xarVarPrepForStore($approved) . "',
              '" . xarVarPrepForStore($type) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $rnid = $dbconn->PO_Insert_ID($releasetable, 'xar_rnid');

    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'create', $rnid, 'rnid');

    // Return the id of the newly created user to the calling process
    return $rnid;

}

?>