<?php
// update ephemerids
function ephemerids_adminapi_update($args)
{
    // Get arguments 
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if ((!isset($did)) ||
        (!isset($eid)) ||
        (!isset($mid)) ||
        (!isset($yid)) ||
        (!isset($content))) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'add', 'empherids');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security Check
    if(!xarSecurityCheck('EditEphemerids')) return;
    $elanguage = 'all';

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ephemtable = $xartable['ephem'];

    $query = "UPDATE $ephemtable
              SET xar_yid       = ?,
                  xar_mid       = ?,
                  xar_did       = ?,
                  xar_content   = ?,
                  xar_elanguage = ?
              WHERE xar_eid = $eid";
    $bindvars = array($yid, $mid, $did, $content, $elanguage, $eid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    return true;
}
?>