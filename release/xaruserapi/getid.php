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
                     xar_regname,
                     xar_displname,
                     xar_desc,
                     xar_type,
                     xar_class,
                     xar_certified,
                     xar_approved,
                     xar_rstate
            FROM $releasetable
            WHERE xar_rid = " . xarVarPrepForStore($rid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($rid, $uid, $regname, $displname, $desc, $type, $class, $certified, $approved, $rstate) = $result->fields;
    $result->Close();

    if (!xarSecurityCheck('OverviewRelease', 0)) {
        return false;
    }

    $releaseinfo = array('rid'        => $rid,
                         'uid'        => $uid,
                         'regname'    => $regname,
                         'displname'  => $displname,
                         'desc'       => $desc,
                         'type'       => $type,
                         'class'      => $class,
                         'certified'  => $certified,
                         'approved'   => $approved,
                         'rstate'     => $rstate);

    return $releaseinfo;
}

?>