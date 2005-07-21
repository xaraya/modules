<?php
/*/
 * createpic
 * creates a new pic from the info recieved from addpic
 *
 * @redirects you to addpic
/*/
function shopping_adminapi_createpic($args)
{
    // security check
    if (!xarSecurityCheck('AddShoppingItems')) return false;

    extract($args);

      // get database setup and items table
      $dbconn =& xarDBGetConn();
      $xartable =& xarDBGetTables();
      $picstable = $xartable['shopping_items_pics'];

      // SQL to insert the item
      $sql = "INSERT INTO $picstable (xar_iid,xar_ipic)
              VALUES (?,?)";
      $bindvars = array($iid, $ipic);
      $result = &$dbconn->Execute($sql,$bindvars);
      if (!$result) return false;

      // close result set
      $result->Close();

    return true;
}
?>