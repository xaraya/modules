<?php
function netquery_adminapi_dellog()
{
    if(!xarSecurityCheck('DeleteNetquery')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $GeoccTable = $xartable['netquery_geocc'];
    $query = "UPDATE $GeoccTable SET users = 0 WHERE users > 0";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    $capture_log_filepath = xarModGetVar('netquery', 'capture_log_filepath');
    if (file_exists($capture_log_filepath))
    {
      unlink($capture_log_filepath);
      touch ($capture_log_filepath);
    }
    return true;
}
?>