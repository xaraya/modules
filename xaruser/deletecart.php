<?php

/*/
 * deletecart function
 * deletes an item from the shopping cart using api fucntion removecart
 *
 * @redirects to view cart
/*/
function shopping_user_deletecart()
{
    // security check
    if (!xarSecurityCheck('ViewShopping')) return;

    // get vars from url
    if(!xarVarFetch('iid', 'isset', $iid, NULL, XARVAR_DONT_SET)) return;

    // if user is logged in call the api function, else just remove the session var
    if (xarUserIsLoggedIn()) {
      if (!xarModAPIFunc('shopping', 'user', 'removecart', array('iid' => $iid))) return;
    } else {
      $totalSessionItems = xarSessionGetVar("NumItems");
      if ($totalSessionItems > 0) {
        for($i = 1; $i <= $totalSessionItems; $i++) {
          // if an item has been deleted, we must move all subsequnt vars down by one in the list
          if (isset($del)) {
            $j = $i - 1;
            xarSessionSetVar("Item.$j.ID", xarSessionGetVar("Item.$i.ID"));
            xarSessionSetVar("Item.$j.Quantity", xarSessionGetVar("Item.$i.Quantity"));
            xarSessionSetVar("Item.$j.Kind", xarSessionGetVar("Item.$i.Kind"));
            xarSessionDelVar("Item.$i.ID");
            xarSessionDelVar("Item.$i.Quantity");
            xarSessionDelVar("Item.$i.Kind");
            continue;
          }

          // if the session vars id is the same as the $iid, delete it
          if (xarSessionGetVar("Item.$i.ID") == $iid) {
            xarSessionDelVar("Item.$i.ID");
            xarSessionDelVar("Item.$i.Quantity");
            xarSessionDelVar("Item.$i.Kind");
            xarSessionSetVar("NumItems", $totalSessionItems - 1);
            $del = true;
          }
        }
      }
    }

    return xarModFunc('shopping','user', 'viewcart');
}
?>
