<?php
// delete a Url
function window_admin_deleteurl($args)
{
    if (!xarSecurityCheck('AdminWindow')) return;
    if (!xarSecConfirmAuthKey()) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    if (!xarVarFetch('id', 'id', $id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('bluff', 'str', $bluff, '', XARVAR_NOT_REQUIRED)) return;
    extract($args);

    $urltable = $xartable['window'];

    // extract info from db
    $query = "DELETE
            FROM
            $urltable
            WHERE xar_id=$id";

    if(!$dbconn->Execute($query)) return;

    xarResponseRedirect(xarModURL('window', 'admin', 'addurl'));
}
?>