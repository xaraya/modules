<?php
/*/
 * swapcart
 * moves an item from the cart to the wishlist and vice versa
 *
 * @arg/@param uid -- the user id of the current user
 * @arg/@param iid -- array of item to move
 * @arg/@param quantity -- array of quantities for the items above
 * @arg/@param kind -- the type of move we are performing (0 = to cart; 1 = to WL); this is an array for the ids above
 *
 * @returns boolean
/*/
function shopping_userapi_swapcart($args)
{
    // security check
    if (!xarSecurityCheck('ViewShopping')) return;

    // get args
    extract($args);
    // check args
    if (!isset($uid)) {
      $uid = xarUserGetVar('uid');
    }
    // requires array for future possibility of bulk swap
    if (!isset($iid) || !is_array($iid)) return false;
    if (!isset($kind) || !is_array($kind)) return false;

    if (count($iid) != count($kind)) return false;

      // get database setup and cart table
      $dbconn =& xarDBGetConn();
      $xartable =& xarDBGetTables();
      $carttable = $xartable['shopping_cart'];

      for ($i = 0; $i < count($iid); $i++) {
          // form sql
          $sql = "UPDATE $carttable
                SET xar_cstatus = ? , xar_iquantity = ?
                WHERE xar_uid = ? AND xar_iid = ?";
          $bindvars = array($kind[$i], 1, $uid, $iid[$i]);
        $result = &$dbconn->Execute($sql,$bindvars);
        if (!$result) return false;
        // close result set
        $result->Close();
      }

    return true;
}
?>