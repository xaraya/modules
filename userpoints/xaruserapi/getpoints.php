<?php
function userpoints_userapi_getpoints($args)
{

extract($args);

    /*if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object id', 'admin', 'createhook', 'userpoints');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'extrainfo', 'admin', 'createhook', 'userpoints');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }*/
// Get database setup

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pointstypestable = $xartable['pointstypes'];

    // Get item
    $query = "SELECT xar_uptid, xar_tpoints
            FROM $pointstypestable
            WHERE xar_module = '$pmodule'
            AND (xar_itemtype = $itemtype OR xar_itemtype = 0) 
            AND xar_action = '$paction'";
     $result =& $dbconn->Execute($query);
    if (!$result) return;
    if ($result->EOF) {
        return false; //no points
    }
    while (!$result->EOF) {
        list($uptid, $tpoints) = $result->fields;
        $result->MoveNext();
        $data['uptid'] = $uptid;
        $data['tpoints'] = $tpoints;
    }
    
    return $data;
}
?>