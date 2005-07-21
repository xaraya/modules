<?php
/*/
 * shopping/xareventapi.php 1.00 July 25th 2003 jared_rich@excite.com
 *
 * Shopping Module Event API File
 *
 * copyright (C) 2003 by Jared Rich
 * license GPL <http://www.gnu.org/licenses/gpl.html>
 * author: Jared Rich
/*/

/*/
 * shopping event handler for the system event ServerRequest
 *
 * this function is called when the system triggers the
 * event in index.php on each Server Request
 *
 * @return: bool
/*/
function shopping_eventapi_OnServerRequest()
{
        if (!xarUserIsLoggedIn()) {
          // check if user has added items to cart yet
          if (!xarSessionGetVar("NumItems")) {
            // set numitem to zero
            xarSessionSetVar("NumItems", 0);
          }
        } else {
          // dump all session items into user cart
          // first check if NumItems is set
          $totalSessionItems = xarSessionGetVar("NumItems");
          if ($totalSessionItems > 0) {
            for($i = 1; $i <= $totalSessionItems; $i++) {
              xarModAPIFunc('shopping', 'user', 'createcart',
                            array('iid' => xarSessionGetVar("Item.$i.ID"),
                                  'quantity' => xarSessionGetVar("Item.$i.Quantity"),
                                  'kind' => xarSessionGetVar("Item.$i.Kind")));
              xarSessionDelVar("Item.$i.ID");
              xarSessionDelVar("Item.$i.Quantity");
              xarSessionDelVar("Item.$i.Kind");
            }
          }
          xarSessionDelVar("NumItems");
        }

        return true;
}

?>
