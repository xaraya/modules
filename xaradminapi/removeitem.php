<?php
/*/
 * removeitem
 * removew an item from the database
 *
 * @returns true or false
/*/
function shopping_adminapi_removeitem($args)
{
    // security check
    if (!xarSecurityCheck('DeleteShoppingItems')) return false;
    // get args
    extract($args);

    if (!isset($iid)) return false;

    // get databse setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $itemstable = $xartable['shopping_items'];

    // set up query and run it
    $sql = "DELETE FROM $itemstable WHERE xar_iid = ?";
    $result = &$dbconn->Execute($sql,array($iid));
    if (!$result) return false;
    $result->Close();

    // call delete hooks
    xarModCallHooks('item', 'delete', $iid, array('module' => 'shopping', 'itemid' => $iid));

    return true;
}
?>