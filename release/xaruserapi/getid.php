<?php

function release_userapi_getid($args)
{
    extract($args);

    if (!isset($rid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'get', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // Get link
    $query = "SELECT xar_rid,
                     xar_uid,
                     xar_name,
                     xar_desc,
                     xar_type,
                     xar_certified,
                     xar_approved
            FROM $releasetable
            WHERE xar_rid = " . xarVarPrepForStore($rid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($rid, $uid, $name, $desc, $type, $certified, $approved) = $result->fields;
    $result->Close();

    if (!xarSecurityCheck('OverviewRelease', 0)) {
        return false;
    }

    $releaseinfo = array('rid'        => $rid,
                         'uid'        => $uid,
                         'name'       => $name,
                         'desc'       => $desc,
                         'type'       => $type,
                         'certified'  => $certified,
                         'approved'   => $approved);

    return $releaseinfo;
}

?>