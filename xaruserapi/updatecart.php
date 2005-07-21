<?php
/*/
 * updatecart
 * updates an items quantity in the cart
 *
 * @arg/@param uid -- the user id of the current user
 * @arg/@param items -- an array of iids and the new quantites
 *
 * @returns boolean
/*/
function shopping_userapi_updatecart($args)
{
    // security check
    if (!xarSecurityCheck('ViewShopping')) return;

    // get args
    extract($args);
    // check args
    if (!isset($uid)) {
      $uid = xarUserGetVar('uid');
    }
    if (!is_array($items) || !isset($items)) return false;

      // get database setup and cart table
      $dbconn =& xarDBGetConn();
      $xartable =& xarDBGetTables();
      $carttable = $xartable['shopping_cart'];

      foreach ($items as $iid => $quantity) {
        if ($quantity > 0 && is_numeric($quantity)) {
          // SQL to update the quantity
          $sql = "UPDATE $carttable SET xar_iquantity = ";
          if (isset($addto)) {
            $sql .= "xar_iquantity + ";
          }
          $sql .= $quantity . " WHERE xar_uid = ? AND xar_iid = ?";

          $result = &$dbconn->Execute($sql,array($uid, $iid));
          if (!$result) return;
          // close result set
          $result->Close();
        }
      }

    return true;
}
?>