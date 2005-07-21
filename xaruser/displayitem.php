<?php
/*/
 * displayitem function
 * displays and item based iid
 *
 * @returns template variables
/*/
function shopping_user_displayitem($args)
{
    // security check
    if (!xarSecurityCheck('ReadShoppingItems')) return;

    // get vars
    if(!xarVarFetch('iid',       'isset', $iid,        NULL, XARVAR_DONT_SET)) return;
    if(!xarVarFetch('phase',     'int:1', $phase,      1,    XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('picphase',  'int:1', $picphase,   1,    XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('recophase', 'int:1', $recophase,  1,    XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('startnum',  'int:1', $startnum,   1,    XARVAR_NOT_REQUIRED)) return;

    // get the args
    extract($args);

    if (!isset($iid)) return;

    // init data array
    $data = array();
    $data['phaseurl'] = xarModURL('shopping', 'user', 'displayitem', array('iid' => $iid));

    // see if user has the permissions
    $viewrecos = false;
    if (xarSecurityCheck('ViewShoppingRecos', 0)) {
      $viewrecos = true;
    }
    $submitrecos = false;
    if (xarSecurityCheck('SubmitShoppingRecos', 0) && xarModGetVar('shopping', 'userecommendations')) {
      $submitrecos = true;
    }
    $addpics = false;
    if (xarSecurityCheck('AddShoppingItems', 0)) {
      $addpics = true;
    }
    $edititem = false;
    if (xarSecurityCheck('EditShoppingItems', 0)) {
      $edititem = true;
    }

    $data['viewrecos'] = $viewrecos;

    // get the item
    $item = xarModAPIFunc('shopping','user','getallitems',
                         array('cids' => true,
                               'where' => array('xar_iid' => array('=' => $iid))));
    // put results of previous function in to the data array
    $data['iid'] = $item[0]['iid'];
    $data['name'] = $item[0]['name'];
    $data['price'] = $item[0]['price'];
    $data['summary'] = $item[0]['summary'];
    $data['desc'] = $item[0]['description'];
    $data['status'] = $item[0]['status'];
    $data['stock'] = $item[0]['stock'];
    $data['buys'] = $item[0]['buys'];

    if (xarModIsHooked('categories', 'shopping')) {
      $data['cids'] = $item[0]['cids'];
    }

    // get the first pic for this item to display
    $pics = xarModAPIFunc('shopping','user','getallpics',
                          array('equals' => $iid));
    if (!$pics) {
      $data['firstpic'] = false;
    } else {
      $data['firstpic'] = $pics[0]['pic'];
    }

    // if the admin has chosen to display stock messages, display them
    if (xarModGetVar('shopping', 'displaylowstock')) {
      if ($data['status'] == 'Low stock') {
        $data['displaylowstock'] = true;
        $data['lowstockmessage'] = xarML('Buy now! Only <u>#(1)</u> left!', $data['stock']);
      } elseif ($data['status'] == 'Backordered') {
        $data['displaylowstock'] = true;
        $data['lowstockmessage'] = xarML('This item is currently on backorder.<br />Order it now and we will send it to you<br />when it comes in.');
      } elseif ($data['status'] == 'Discontinued') {
        $data['displaylowstock'] = true;
        $data['lowstockmessage'] = xarML('This item has been discontinued and cannot be bought.');
      }
    }

    // if the item is discontinued, you can't buy it!
    if ($data['status'] == 'Discontinued') {
      $data['nobuy'] = true;
    }

    // check for ViewShopping Permission to see if user can add to cart or wishlist
    if (xarSecurityCheck('ViewShopping', 0)) {
      $data['addcarturl'] = xarModURL('shopping', 'user', 'addcart', array('iid' => $iid));
      $data['addwishurl'] = xarModURL('shopping', 'user', 'addcart', array('kind' => 1, 'iid' => $iid));
    } else {
      $data['nobuy'] = true;
    }

    // do not display the hitcount, just save it in the variable cache.
    if (xarModIsHooked('hitcount','shopping')) {
        xarVarSetCached('Hooks.hitcount','save',1);
        $hitshooked = true;
    } else {
        $hitshooked = false;
    }

    // call display hooks
    $data['hooks'] = xarModCallHooks('item', 'display', $iid,
                                     array('returnurl' => xarModURL('shopping',
                                                                    'user',
                                                                    'displayitem',
                                                                    array('iid' => $iid,
                                                                          'phase' => $phase))),
                                     'shopping');

    // get the hitcount from the cache and store it in the data array
    if ($hitshooked && xarVarIsCached('Hooks.hitcount', 'value')) {
      $data['hits'] = xarVarGetCached('Hooks.hitcount', 'value');
    } else {
      $data['hits'] = 0;
    }

    // this switch display the dynamic body portion of the template
    $body = "";
    switch ($phase) {
      default:
      case 1:
          // display the details
          $data['descsubdata'] = array('iid' => $iid,
                                       'desc' => $data['desc'],
                                       'summary' => $data['summary']);
          $data['viewrecos'] = $viewrecos;
          
          // only if user can view recos
          if ($viewrecos) {
              // get all recos
              $recos = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                     array('where' => array('xar_iid1' => array('=' => $iid),
                                                            'xar_iid2' => array('=' => $iid))));
              if (is_array($recos)) {
                  // get a count of all recos
                  $count = count($recos);
                  // pick three different random numbers in that range
                  if ($count >= 1) {
                      $num1 = rand(0, $count-1);
                      $randrecos[0] = $recos[$num1];
                  }
                  if ($count >= 2) {
                      do {
                          $num2 = rand(0, $count-1);
                          $randrecos[1] = $recos[$num2];
                      } while ($num2 == $num1);
                  }
                  if ($count >= 3) {
                      do {
                          $num3 = rand(0, $count-1);
                          $randrecos[2] = $recos[$num3];
                      } while ($num3 == $num2 || $num3 == $num1);
                  }

                  $data['recos'] = array();
                  foreach($randrecos as $reco) {
                      // if reco['iid1'] == this item, use iid2 info
                      if ($reco['iid1'] == $iid) {
                          $reco['thisitem'] = $reco['iid1'];
                          $reco['iid'] = $reco['iid2'];
                          $reco['name'] = $reco['name2'];
                      } else { // opposite of above
                          $reco['thisitem'] = $reco['iid2'];
                          $reco['iid'] = $reco['iid1'];
                          $reco['name'] = $reco['name1'];
                      }

                      // get the first pic for this item to display
                      $pics = xarModAPIFunc('shopping','user','getallpics',
                                            array('equals' => $reco['iid']));

                      if (!$pics) {
                          // no pics exist for the recommended item
                          $reco['firstpic'] = false;
                      } else {
                          // first pic for the item
                          $reco['firstpic'] = $pics[0]['pic'];
                      }
                      $data['recos'][] = $reco;
                  }
                  $data['moreurl'] = xarModURL('shopping', 'user', 'displayitem', array('iid' => $iid, 'phase' => 4));
              } 
          }
          break;
      case 2:
          $data['doadmincomments'] = ($addpics || $submitrecos || $edititem);
          $data['edititem'] = $edititem;
          $data['addpics'] = $addpics;
          $data['submitrecos'] = $submitrecos;
        break;
      case 3:
          $data['dopics'] = ($picphase == 1 ) || $addpics;
          $data['picsubdata'] = array('iid' => $iid);
          if ($picphase == 1) {
            $data['picfunc'] = 'viewpics';
            $data['picfunctype'] = 'user';
        } else {
          if ($addpics) {
              $data['picfunc'] = 'addpic';
              $data['picfunctype'] = 'admin';
          }
        }
        break;
      case 4:
          $data['dorecos'] = ($recophase == 1) || $submitrecos;
          if ($recophase == 1) {
              $data['recosfunc'] = 'viewrecos';
              $data['recossubdata'] = array('iid' => $iid, 'startnum' => $startnum);  
          } elseif ($submitrecos) {
              $data['recosfunc'] = 'addreco';
              $data['recossubdata'] = array('iid' => $iid);
          }
        break;
    }
    $data['phase'] = $phase;
    // insert the body into the data array
    $data['body'] = $body;

    return $data;
}
?>