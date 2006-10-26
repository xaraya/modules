<?php
function netquery_admin_bbdelete()
{
    if (!xarSecurityCheck('DeleteNetquery')) return;
    $xartable =& xarDBGetTables();
    $SpamblockerTable = $xartable['netquery_spamblocker'];
    $dbconn =& xarDBGetConn();
    if (isset($_POST['DelAll']) AND $_POST['DelAll'] == xarML('Delete All'))
    {
        $query = "TRUNCATE TABLE $SpamblockerTable";
        $result =& $dbconn->Execute($query);
        if (!$result) return;
        $result->Close();
    }
    else if (isset($_POST['DelSel']) AND $_POST['DelSel'] == xarML('Delete Selected'))
    {
        if (!xarVarFetch('selection', 'array', $selection, array(), XARVAR_NOT_REQUIRED)) return;
        $selected = implode(",", $selection);
        $query = "DELETE FROM $SpamblockerTable WHERE id IN ($selected)";
        $result =& $dbconn->Execute($query);
        if (!$result) return;
        $result->Close();
    }
    xarResponseRedirect(xarModURL('netquery', 'admin', 'bblogedit'));
    return;
}
?>