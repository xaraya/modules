<?php
/*/
 * showitems function
 * displays items based on category (if hooked)
 *
 * @returns template variables
/*/
function shopping_user_showitems($args)
{
    // security check
    if (!xarSecurityCheck('ViewShoppingItems')) return;

    // get the options from the url
    if(!xarVarFetch('startnum',  'isset', $startnum,  1,     XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('catid',     'isset', $catid,     NULL,  XARVAR_NOT_REQUIRED)) return;

    // get the args
    extract($args);

    // init data array
    $data = array();

    // get number of item to display per page
    $numitems = xarModGetVar('shopping', 'useritemsperpage');

    // checks to see if we are using categories in the shopping module
    $catids=0;
    if (xarModIsHooked('categories','shopping')) {
        if (!isset($catid)) {
            $catids = 0;
        } elseif (eregi('-', $catid)) {
            $catids = explode('-', $catid);
        } elseif (eregi('[[:space:]]', $catid)) {
            $catids = explode(' ', $catid);
        } else {
            $catids = array($catid);
        }
        $data['cids'] = $catids;
    }

    // get items and pager
    $items = xarModAPIFunc('shopping', 'user', 'getallitems',
                                array('startnum' => $startnum,
                                      'numitems' => $numitems,
                                      'cids' => $catids,
                                      'catid' => $catid,
                                      'order' => array('xar_iname' => 'ASC')));
      $data['pager'] = xarTplGetPager($startnum,
                              xarModAPIFunc('shopping', 'user', 'countitems',
                                            array('catid' => $catid)),
                              xarModURL('shopping', 'user', 'showitems',
                                        array('startnum' => '%%',
                                              'cids' => $catids)),
                              $numitems);

    // check to see if there are any items
    $data['items'] = array();
    if (is_array($items)) {
      foreach ($items as $item) {
         // get the first pic for each item
         $pics = xarModAPIFunc('shopping', 'user', 'getallpics',
                               array('equals' => $item['iid']));
         if (!$pics) {
           $item['pic'] = false;
         } else {
           $item['pic'] = $pics[0]['pic'];
         }

         // set the url for the item
         $item['url'] = xarModURL('shopping', 'user', 'displayitem', array('iid' => $item['iid']));

         // check status to see if item can be added to cart
         if ($item['status'] == 'Discontinued') {
           $item['nobuy'] = true;
         }

         // check for view shopping permission... and let buy if so, else no buy for you!
         if (xarSecurityCheck('ViewShopping', 0)) {
           $item['buyurl'] = xarModURL('shopping', 'user', 'addcart', array('iid' => $item['iid']));
         } else {
           $item['nobuy'] = true;
         }

         // send the item to a template to be displayed
         $data['items'][$item['iid']] = $item;

      }
    }
    return $data;
}
?>