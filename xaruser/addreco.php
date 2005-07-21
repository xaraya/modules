<?php

/*/
 * addreco function
 * displays the form that gets the info for a new recommendation
 *
 * @returns template variables
/*/
function shopping_user_addreco($args)
{
    // security check
    if (!(xarSecurityCheck('DeleteShoppingRecos', 0) || (xarSecurityCheck('SubmitShoppingRecos') && xarModGetVar('shopping', 'userecommendations')))) return;

    // get args
    extract($args);
    if (!isset($iid)) return;
    if (!isset($showbox)) $showbox = true;

    // init data array and generate authorization key
    $data = array();
    $data['authid'] = xarSecGenAuthKey();
    $data['showbox'] = $showbox;
    $data['iid'] = $iid;

    // set submit button label
    $data['submitlabel'] = xarML('Add Recommendations');

    // if user has additem permission, display a link to do so
    if (xarSecurityCheck('AddShoppingItems', 0)) {
      $data['addpicurl'] = xarModURL('shopping', 'user', 'displayitem',
                                     array('iid' => $iid,
                                           'phase' => 3,
                                           'picphase' => 2));
    }
    // if user has edit item permission allow them
    if (xarSecurityCheck('EditShoppingItems', 0)) {
      $data['editurl'] = xarModURL('shopping', 'admin', 'edititem',
                                     array('iid' => $iid));
    }


    // get all items except this item
    $items = xarModAPIFunc('shopping', 'user', 'getallitems',
                           array('order' => array('xar_iname' => 'ASC'),
                                 'where' => array('xar_iid' => array('!=' => $iid))));
    // get all recos for this item
    $recos = xarModAPIFunc('shopping', 'user', 'getallrecos',
                           array('where' => array('xar_iid1' => array('=' => $iid),
                                                  'xar_iid2' => array('=' => $iid))));

    // if items and recos were returned
    // loop trough each array and check to see if iids match
    // if they do not, add them to the $data['items'] array
    if (is_array($items)) {
      if (is_array($recos)) {
        foreach ($items as $item) {
          foreach ($recos as $reco) {
            if (($item['iid'] == $reco['iid1']) || ($item['iid'] == $reco['iid2'])) {
              // make the falg false so the item will not be added
              // this is necessary because if we have an item that does not have an
              // id equal to the first reco, but it is the second... we dont' want to add it
              $addflag = false;
              break;
            } else {
              $addflag = true;
            }
          }
          if ($addflag) {
            $data['items'][] = $item;
          }
        }
      } else {
        // if no recos exist for an item, simply add all items to the list
        foreach ($items as $item) {
          $data['items'][] = $item;
        }
      }
    }



    // check to see if it is an array
    if (isset($data['items'])) {
      $data['itemarray'] = true;
    }
    $data['thisitem'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                      array('where' => array('xar_iid' => array('=' => $iid))));

    return $data;
}
?>
