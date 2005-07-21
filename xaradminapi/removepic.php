<?php
/*/
 * removepic
 * removes a pic from an item
 *
 * @returns true or false
/*/
function shopping_adminapi_removepic($args)
{
    // security check
    if (!xarSecurityCheck('DeleteShoppingItems')) return false;
    // get args
    extract($args);

    if (!isset($iid)) return false;

    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $picstable = $xartable['shopping_items_pics'];

    // set up query and run it
    $bindvars = array();
    $sql = "DELETE FROM $picstable WHERE xar_iid = ?";
    $bindvars[] = $iid;
    if (isset($pic)) {
        $sql .= " AND xar_ipic = ?";
        $bindvars[] = $pic;
    }
    $result = &$dbconn->Execute($sql,$bindvars);
    if (!$result) return false;

    return true;
}
?>