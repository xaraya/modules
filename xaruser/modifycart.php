<?php

/*/
 * modifycart -- gets new quantity values from viewcart and updates them in the databse or session
 *
 * @returns template variables
/*/
function shopping_user_modifycart()
{
    // security check
    if (!xarSecurityCheck('ViewArticles')) return;

    // check for params
    if (!xarVarFetch('quantitems', 'isset', $items, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!isset($items) || !is_array($items)) {
      return;
    }

    if (xarUserIsLoggedIn()) {
      if (!xarModAPIFunc('shopping', 'user', 'updatecart',
                         array('uid' => xarUserGetVar('uid'),
                               'items' => $items))) return false;
    } else { // user is not logged in
      $totalSessionItems = xarSessionGetVar("NumItems");
      // loop though all session vars
      for($i = 1; $i <= $totalSessionItems; $i++) {
        // if the item is not in the cart, skip and contine with next item
        if (xarSessionGetVar("Item.$i.Kind") == 1) continue;
        // loop through all items passed in
        foreach ($items as $iid => $quantity) {
          if (xarSessionGetVar("Item.$i.ID") == $iid) {
            if ($quantity > 0 && is_numeric($quantity)) {
              xarSessionSetVar("Item.$i.Quantity", $quantity);
            }
          }
        }
      }
    }

    return xarModFunc('shopping', 'user', 'viewcart');
}
?>
