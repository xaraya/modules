<?php
function netquery_admin_bbdelid()
{
    if (!xarSecurityCheck('DeleteNetquery')) return;
    if (!xarVarFetch('id','int',$id)) return;
    $xartable =& xarDBGetTables();
    $spamblockerTable = $xartable['netquery_spamblocker'];
    $dbconn =& xarDBGetConn();
    $query = "DELETE FROM $spamblockerTable WHERE id = ?";
    $result =& $dbconn->Execute($query, array((int)$id));
    if (!$result) return;
    $result->Close();
    xarResponseRedirect(xarModURL('netquery', 'admin', 'bblogedit'));
    return;
}
?>