<?php

/*/
 * addcart function
 * adds an item to a cart or wishlist
 *
 * @param iid - the id of the item to add
 * @param type - 0 = cart; 1 = wishlist
 *
 * @returns template variables
/*/
function shopping_user_addcart()
{
    // security check
    if (!xarSecurityCheck('ViewShopping')) return;

    // get params
    if(!xarVarFetch('iid',  'isset', $iid,  NULL,      XARVAR_DONT_SET)) return;
    if(!xarVarFetch('kind', 'isset', $kind, 0,         XARVAR_NOT_REQUIRED)) return;

    // check params
    if (!isset($iid)) return;

    if (xarUserIsLoggedIn()) {
      if (!xarModAPIFunc('shopping', 'user', 'createcart',
                         array('iid' => $iid,
                               'kind' => $kind))) return;
    } else {
      // get number of item added during anon session and add one
      $totalSessionItems = xarSessionGetVar("NumItems") + 1;

      // loop through all session item and check if iid already exists
      // if it does, just add one to quantity otherwise add item
      $newitem = true;
      for ($i = 1; $i < $totalSessionItems; $i++) {
        if (xarSessionGetVar("Item.$i.ID") == $iid) {
          $newitem = false;
          break;
        }
      }

      if ($newitem){
        // set number of session items added
        xarSessionSetVar("NumItems", $totalSessionItems);
        // add this item to session
        xarSessionSetVar("Item.$totalSessionItems.ID", $iid);
        xarSessionSetVar("Item.$totalSessionItems.Kind", $kind);
        xarSessionSetVar("Item.$totalSessionItems.Quantity", 1);
      } else {
        if ($kind == 0) { // if user clicked "add to cart"
          if (xarSessionGetVar("Item.$i.Kind") == 0) { // if the item is in the cart
            // add one to quantity
            xarSessionSetVar("Item.$i.Quantity", xarSessionGetVar("Item.$i.Quantity") + 1);
          } else { // if the item is in the wishlist
            // move the item to the cart
            xarSessionSetVar("Item.$i.Kind", 0);
          }
        } else { // if the user clicked "add to wishlist"
          if (xarSessionGetVar("Item.$i.Kind") == 0) { // if the item is in the cart (if the item is in the WL nothing happens
            // move the item to the wishlist and set the quantity to one
            xarSessionSetVar("Item.$i.Kind", 1);
            xarSessionSetVar("Item.$i.Quantity", 1);
          }
        }
      }
    }

    // goto where we came from
    xarResponseRedirect($_SERVER['HTTP_REFERER']);

    return true;
}
?>
