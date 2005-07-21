<?php

/*/
 * changestatus function
 * changes the status of items when low stock theshold changes
 *
 * @returns true or false
/*/
function shopping_adminapi_changestatus()
{
    $lowstock = xarModGetVar('shopping', 'lowstock');

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $itemstable = $xartable['shopping_items'];

    $sql = "UPDATE $itemstable SET xar_istatus=1 WHERE xar_istock BETWEEN 1 AND ?";
    $result = &$dbconn->Execute($sql,array($lowstock));
    if (!$result) return false;

    $sql = "UPDATE $itemstable SET xar_istatus=0 WHERE xar_istock > ?";
    $result = &$dbconn->Execute($sql,array($lowstock));
    if (!$result) return false;
    return true;
}
?>
