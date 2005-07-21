<?php
/*/
 * createcart function
 * creates a new item in a users shopping cart or wishlist
 *
 * @arg iid - the id of the item being created
 * @arg type - 0 = cart; 1 = wishlist
 * @arg quantity - the amount of the item to create
 *
 * @returns boolean
/*/
function shopping_userapi_createcart($args)
{
    // security check
    if (!xarSecurityCheck('ViewShopping')) return false;

    // get args
    extract($args);

    // check args
    if (!isset($iid)) return false;
    if (!isset($kind)) $kind = 0;
    if (!isset($quantity)) $quantity = 1;

    // get the current userid from the database
    $uid = xarUserGetVar('uid');

    // check kind and if item is already in cart.
    $items = xarModAPIFunc('shopping', 'user', 'getallcart',
                           array('uid' => $uid));
    $newitem = true;
    // if there are some items
    if ($items != false) {
      foreach ($items as $item) {
        if ($item['iid'] == $iid) {
          $newitem = false;
          break;
        }
      }
    }

    if (!$newitem) { // item is already in the cart
      if ($kind == 0) { // if user clicked "add to cart" $kind = 0
        if ($item['kind'] == 0) { // if item is in cart
          // add $quantity to quantity
          xarModAPIFunc('shopping', 'user', 'updatecart',
                        array('addto' => true,
                              'uid' => $uid,
                              'items' => array($iid => $quantity)));
        } else { // if item is in WL
          // move to cart
          xarModAPIFunc('shopping', 'user', 'swapcart',
                        array('uid' => $uid,
                              'iid' => array($iid),
                              'kind' => array($kind)));
        }
      } else { // if user clicked "add to wishlist" $kind = 1
        if ($item['kind'] == 0) { // if item is in cart
          // move to wishlist
          xarModAPIFunc('shopping', 'user', 'swapcart',
                        array('uid' => $uid,
                              'iid' => array($iid),
                              'kind' => array($kind)));
        }
      }
    } else { // item is new
      // get database setup and cart table
      $dbconn =& xarDBGetConn();
      $xartable =& xarDBGetTables();
      $carttable = $xartable['shopping_cart'];

        // SQL to insert the item
        $sql = "INSERT INTO $carttable (xar_uid,xar_iid,xar_iquantity,xar_cstatus)
                VALUES (?,?,?,?)";
        $bindvars = array($uid, $iid, $quantity, $kind);
        $result =& $dbconn->Execute($sql,$bindvars);
        if (!$result) return false;
        // close result set
        $result->Close();
    }

    return true;
}
?>