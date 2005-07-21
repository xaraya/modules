<?php

/*/
 * passes individual menu items to the main user menu
 *
 * @returns array containing the menulinks for the main menu items.
/*/
function shopping_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('ViewShopping',0)) {
        $menulinks[] = Array('url'   => xarModURL('shopping',
                                                  'user',
                                                  'viewcart'),
                             'title' => xarML('View your shopping cart'),
                             'label' => xarML('View Cart'));

        if (xarModIsHooked('categories', 'shopping') && xarModGetVar('shopping', 'mastercids') != "") {
          $menulinks[] = Array('url'   => xarModURL('shopping',
                                                    'user',
                                                    'showitems',
                                                    array('catid' => xarModGetVar('shopping', 'featurecat'))),
                               'title' => xarML('View items for sale'),
                               'label' => xarML('View Items'));
        } else {
                  $menulinks[] = Array('url'   => xarModURL('shopping',
                                                    'user',
                                                    'showitems'),
                               'title' => xarML('View items for sale'),
                               'label' => xarML('View Items'));
        }

         //$menulinks[] = Array('url'   => xarModURL('shopping',
         //                                         'user',
         //                                         'viewprofile'),
         //                    'title' => xarML('View your shopping profile'),
         //                    'label' => xarML('Your Account'));
         $menulinks[] = Array('url'   => xarModURL('shopping',
                                                  'user',
                                                  'viewpolicy'),
                             'title' => xarML('View the shipping and return policies'),
                             'label' => xarML('Policies'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>
