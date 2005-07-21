<?php
/*/
 * removecart function
 * removew an item from the shopping cart
 *
 * @returns true or false
/*/
function shopping_userapi_removecart($args)
{
    // security check
    if (!xarSecurityCheck('ViewShopping')) return false;
    // get args
    extract($args);

    if (!isset($iid)) return false;

    // get databse setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $carttable = $xartable['shopping_cart'];

    // set up query and run it
    $sql = "DELETE FROM $carttable WHERE xar_iid = ?";
    $result = &$dbconn->Execute($sql,array($iid));
    if (!$result) return false;
    $result->Close();

    return true;
}
?>