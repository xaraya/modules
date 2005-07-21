<?php
/*/
 * movecart function
 * moves an item from the cart to the wishlist and vice versa using api function swap cart
 *
 * @arg/@param uid -- the user id of the current user
 * @arg/@param iid -- array of item to move
 * @arg/@param kind -- the type of move we are performing (0 = to cart; 1 = to WL); this is an array for the ids above
 *
 * @returns boolean
/*/
function shopping_user_movecart()
{
    // security check
    if (!xarSecurityCheck('ViewShopping')) return;

    // get params
    if(!xarVarFetch('iid',  'isset', $iid,  NULL, XARVAR_DONT_SET)) return;
    if(!xarVarFetch('kind', 'isset', $kind, 0,    XARVAR_NOT_REQUIRED)) return;

    // check kind
    if ($kind < 0 || $kind > 1) {
      $kind = 0;
    }

    if (xarUserIsLoggedIn()) {
      $uid = xarUserGetVar('uid');

      if (!xarModAPIFunc('shopping', 'user', 'swapcart',
                         array('uid' => $uid,
                               'iid' => array($iid),
                               'kind' => array($kind))))  return;

    } else { // user is not logged in
      $totalSessionItems = xarSessionGetVar("NumItems");
      for($i = 1; $i <= $totalSessionItems; $i++) {
        if (xarSessionGetVar("Item.$i.ID") == $iid) {
          xarSessionSetVar("Item.$i.Kind", $kind);
        }
      }
    }

    return xarModFunc('shopping', 'user', 'viewcart');
}
?>