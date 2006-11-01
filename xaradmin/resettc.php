<?php
function netquery_admin_resettc($args)
{
    if (!xarSecurityCheck('EditRole')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $GeoccTable = $xartable['netquery_geocc'];
    $query = "UPDATE $GeoccTable SET users = 0 WHERE users > 0";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    xarResponseRedirect(xarModURL('netquery', 'admin', 'config'));
    return true;
}
?>