<?php
/*/
 * viewcart function
 * displays teh shopping cart and wishlist
 *
 * @returns template variables
/*/
function shopping_user_viewcart($args)
{
    // security check
    if (!xarSecurityCheck('ViewShopping')) return;

    // get the args
    extract($args);

    // init data array
    $data = array();
    // init all recos array
    $recos = array();
    // init useable recos array
    $userecos = array();

    // check to see if a user is logged in
    if (xarUserIsLoggedIn()) {
      // get the current user
      $uid = xarUserGetVar('uid');
      // get the items
      $data['cartitems'] = xarModAPIFunc('shopping', 'user', 'getallcart',
                                 array('uid' => $uid,
                                       'status' => 'cart'));
      $data['wlitems'] = xarModAPIFunc('shopping', 'user', 'getallcart',
                                 array('uid' => $uid,
                                       'status' => 'wishlist'));

      // if there are items in the cart calulate the subtotal
      if ($data['cartitems'] != false) {
        $subtotal = 0;
        for ($i = 0; $i < count($data['cartitems']); $i++){
          $subtotal += $data['cartitems'][$i]['price'] * $data['cartitems'][$i]['quantity'];
          // format the price for display
          $data['cartitems'][$i]['price'] = round($data['cartitems'][$i]['price'], 2);
          $data['cartitems'][$i]['price'] = '$' . number_format($data['cartitems'][$i]['price'], 2, '.', '');

          // get all recos for the cartitems
          $cartrecos = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                       array('where' => array('xar_iid1' => array('=' => $data['cartitems'][$i]['iid']),
                                                              'xar_iid2' => array('=' => $data['cartitems'][$i]['iid']))));
          // if recos exist, push them into the reco array
          if ($cartrecos != false) {
            foreach ($cartrecos as $cartreco) {
              array_push($recos, $cartreco);
            }
          }
        }

      // format the subtotal for display
      $subtotal = round($subtotal, 2);
      $subtotal = '$' . number_format($subtotal, 2, '.', '');
      $data['subtotal'] = $subtotal;
      }

      // if there are items in the wishlist format the price
      if ($data['wlitems'] != false) {
        for ($i = 0; $i < count($data['wlitems']); $i++){
          $data['wlitems'][$i]['price'] = round($data['wlitems'][$i]['price'], 2);
          $data['wlitems'][$i]['price'] = '$' . number_format($data['wlitems'][$i]['price'], 2, '.', '');

          // get all recos for the wishlistitems
          $wlrecos = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                       array('where' => array('xar_iid1' => array('=' => $data['wlitems'][$i]['iid']),
                                                              'xar_iid2' => array('=' => $data['wlitems'][$i]['iid']))));
          // if recos exist, push them into the reco array
          if ($wlrecos != false) {
            foreach ($wlrecos as $wlreco) {
              array_push($recos, $wlreco);
            }
          }
        }
      }
    } else { // User is not logged in
      // set var to displat login message
      $data['notlogged'] = true;

      // get total number of item added while anonymous
      $totalSessionItems = xarSessionGetVar("NumItems");
      if ($totalSessionItems > 0) {
        $subtotal = 0;
        for($i = 1; $i <= $totalSessionItems; $i++) {
          // get session vars into other vars to aviod having to call the function more than once
          $sessID = xarSessionGetVar("Item.$i.ID");
          $sessQuan = xarSessionGetVar("Item.$i.Quantity");
          $sessKind = xarSessionGetVar("Item.$i.Kind");

          // get the name and price of the item
          $sessItem = xarModAPIFunc('shopping', 'user', 'getallitems',
                                    array('where' => array('xar_iid' => array('=' => $sessID))));
          if (!is_array($sessItem)) return false;
          $sessName = $sessItem[0]['name'];
          $sessPrice = $sessItem[0]['price'];

          // if the kind is 0 it is a cart item, 1 is a wishlist item
          if ($sessKind == 0) {
            $data['cartitems'][] = array('iid' => $sessID,
                                         'quantity' => $sessQuan,
                                         'name' => $sessName,
                                         'price' => $sessPrice);
            // unformat the price and calculate the subtotal
            $ufprice = eregi_replace('\$', '', $sessPrice);
            $subtotal += $ufprice * $sessQuan;
          } else {
            $data['wlitems'][] = array('iid' => $sessID,
                                       'name' => $sessName,
                                       'price' => $sessPrice);
          }
        }
        $data['subtotal'] = '$' . number_format($subtotal, 2, '.', '');

        // if there were no cart or wishlist item, the respective var must be set to false
        if (!isset($data['cartitems'])) {
          $data['cartitems'] = false;
        }
        if (!isset($data['wlitems'])) {
          $data['wlitems'] = false;
        }
      } else {
        $data['cartitems'] = false;
        $data['wlitems'] = false;
      }

      if ($data['cartitems'] != false) {
        foreach ($data['cartitems'] as $cartitem) {
          $cartrecos = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                       array('where' => array('xar_iid1' => array('=' => $cartitem['iid']),
                                                              'xar_iid2' => array('=' => $cartitem['iid']))));
          // if recos exist, push them into the reco array
          if ($cartrecos != false) {
            foreach ($cartrecos as $cartreco) {
              array_push($recos, $cartreco);
            }
          }
        }
      }

      if ($data['wlitems'] != false) {
        foreach ($data['wlitems'] as $wlitem) {
          $wlrecos = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                       array('where' => array('xar_iid1' => array('=' => $wlitem['iid']),
                                                              'xar_iid2' => array('=' => $wlitem['iid']))));
          // if recos exist, push them into the reco array
          if ($wlrecos != false) {
            foreach ($wlrecos as $wlreco) {
              array_push($recos, $wlreco);
            }
          }
        }
      }
    }

    // The following is done for both user logged in and not logged in

      // start getting reco
      // loop through all recos
      for ($i = 0; $i < count($recos); $i++) {
        if ($data['cartitems'] != false) {
          // loop through cart items
          foreach ($data['cartitems'] as $cartitem) {
            // if iid or iid is the same as an itme in the cart, mark it
            if ($recos[$i]['iid1'] == $cartitem['iid']) {
              $recos[$i]['marked1'] = 1;
            }
            if ($recos[$i]['iid2'] == $cartitem['iid']) {
              $recos[$i]['marked2'] = 1;
            }
          }
        }
        // loop through wishlist items and do the same above
        if ($data['wlitems'] != false) {
          foreach ($data['wlitems'] as $wlitem) {
            if ($recos[$i]['iid1'] == $wlitem['iid']) {
              $recos[$i]['marked1'] = 1;
            }
            if ($recos[$i]['iid2'] == $wlitem['iid']) {
              $recos[$i]['marked2'] = 1;
            }
          }
        }
        // if iid1 and iid2 are both marked for the reco
        if (isset($recos[$i]['marked1']) && isset($recos[$i]['marked2'])) {
          continue; // do not push anything on the useable array
        } elseif (isset($recos[$i]['marked1'])) { // only iid 1 is marked
          // make sure we do not add redundant iids
            $noadd = false;
          foreach ($userecos as $reco) {
            if ($reco == $recos[$i]['iid2']) {
              $noadd = true;
              break;
            }
          }
          if (!$noadd) {
            array_push($userecos, $recos[$i]['iid2'], $recos[$i]['name2']); // iid2 is useable
          }
        } else { // only iid2 is marked
          // make sure we do not add redundant iids
            $noadd = false;
          foreach ($userecos as $reco) {
            if ($reco == $recos[$i]['iid1']) {
              $noadd = true;
              break;
            }
          }
          if (!$noadd) {
            array_push($userecos, $recos[$i]['iid1'], $recos[$i]['name1']); // iid1 is useable
          }
        }
      }

      // get four random reco numbers
      $count = count($userecos);
      if ($count / 2 >= 4) {
          // get 1st
          do {
            $num[0] = rand(0, $count-1);
          } while ($num[0] % 2 != 0); // must be an even numer
          // get 2nd
          do {
            $num[1] = rand(0, $count-1);
          } while (($num[1] == $num[0]) || $num[1] % 2 != 0);
          // get 3rd
          do {
            $num[2] = rand(0, $count-1);
          } while (($num[2] == $num[1] || $num[2] == $num[0]) || $num[2] % 2 != 0);
          // get 4th
          do {
            $num[3] = rand(0, $count-1);
          } while (($num[3] == $num[2] || $num[3] == $num[1] || $num[3] == $num[0]) || $num[3] % 2 != 0);

          // display the random recos
          $data['recos'] = array();
          foreach ($num as $i) {
            $dispreco['iid'] = $userecos[$i];
            $dispreco['name'] = $userecos[$i + 1];

            // get the first pic for this item to display
            $pics = xarModAPIFunc('shopping','user','getallpics',
                                  array('equals' => $userecos[$i]));

            if (!$pics) {
              // no pics exist for the recommended item
              $dispreco['firstpic'] = false;
            } else {
              // first pic for the item
              $dispreco['firstpic'] = $pics[0]['pic'];
            }
            $data['recos'][] = $dispreco;
          }
      } else { // get four random items form that table.... or at least at many as you can get up to 4
        $items = xarModAPIFunc('shopping', 'user', 'getallitems');
        $count = count($items);
          if ($count >= 1) {
            $num[0] = rand(0, $count-1);
            $userecos[0] = $items[$num[0]]['iid'];
            $userecos[1] = $items[$num[0]]['name'];
          }
          if ($count >= 2) {
            do {
              $num[1] = rand(0, $count-1);
              $userecos[2] = $items[$num[1]]['iid'];
              $userecos[3] = $items[$num[1]]['name'];
            } while ($num[1] == $num[0]);
          }
          if ($count >= 3) {
            do {
              $num[2] = rand(0, $count-1);
              $userecos[4] = $items[$num[2]]['iid'];
              $userecos[5] = $items[$num[2]]['name'];
            } while ($num[2] == $num[1] || $num[2] == $num[0]);
          }
          if ($count >= 4) {
            do {
              $num[3] = rand(0, $count-1);
              $userecos[6] = $items[$num[3]]['iid'];
              $userecos[7] = $items[$num[3]]['name'];
            } while ($num[3] == $num[2] || $num[3] == $num[1] || $num[3] == $num[0]);
          }

          // display the items
          $data['recos'] = array();
          for ($i = 0; $i < count($userecos); $i += 2) {
            $dispreco['iid'] = $userecos[$i];
            $dispreco['name'] = $userecos[$i + 1];

            // get the first pic for this item to display
            $pics = xarModAPIFunc('shopping','user','getallpics',
                                  array('equals' => $userecos[$i]));

            if (!$pics) {
              // no pics exist for the recommended item
              $dispreco['firstpic'] = false;
            } else {
              // first pic for the item
              $dispreco['firstpic'] = $pics[0]['pic'];
            }
            $data['recos'][] = $dispreco;
          }

      }

      // END getting recos

    // get images for delete and move
    $data['delimg'] = xarTplGetImage('delete.gif');
    $data['wishimg'] = xarTplGetImage('wish.gif');
    $data['cartimg'] = xarTplGetImage('wish.gif');

    // get urls for delete, move, and update
    $data['delurl'] = xarModURL('shopping', 'user', 'deletecart');
    $data['movewishurl'] = xarModURL('shopping', 'user', 'movecart', array('kind' => 1));
    $data['movecarturl'] = xarModURL('shopping', 'user', 'movecart', array('kind' => 0));
    $data['updateurl'] = xarModURL('shopping', 'user', 'modifycart');

    // get payment methods and create string
    $paymethods = array();
    if (xarModGetVar('shopping', 'acceptpaypal')) {
      $paymethods[] = 'PayPal';
    }
    if (xarModGetVar('shopping', 'acceptcredit')) {
      $paymethods[] = 'credit card';
    }
    if (xarModGetVar('shopping', 'acceptbill')) {
      $paymethods[] = 'check or money order';
    }
    $data['paymethods'] = join(', ', $paymethods);

    return $data;
}
?>