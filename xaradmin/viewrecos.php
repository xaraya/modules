<?php
/*/
 * viewrecos function
 * displays recos based on filtering options
 * also acts as a search for recos
 *
 * @returns template variables
 *
 * $search values: 0=all 1=rid
 * $sort values: same as above, except 0 is not ever used
 * $sortorder: ASC or DESC
/*/
function shopping_admin_viewrecos($args)
{
    // security check
    if (!xarSecurityCheck('DeleteShoppingRecos')) return;

    // get the options from the url
    if(!xarVarFetch('startnum',  'isset', $startnum,  1,     XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('catid',     'isset', $catid,     NULL,  XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('sort',      'isset', $sort,      1,     XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('sortorder', 'isset', $sortorder, 'ASC', XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('search',    'isset', $search,    0,     XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('option1',   'isset', $option1,   NULL,  XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('option2',   'isset', $option2,   NULL,  XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('errmsg',    'isset', $errmsg,    NULL,  XARVAR_NOT_REQUIRED)) return;

    // extract args
    extract($args);

    // init data array
    $data = array();
    $data['search'] = $search;
    $data['sort'] = $sort;
    $data['sortorder'] = $sortorder;
    $data['catid'] = $catid;

    // checks to see if we are using categories in the shopping module
    if (xarModIsHooked('categories','shopping')) {
      $data['iscats'] = true;
    }

    // check for an error message in the url
    if (isset($errmsg)) {
      $data['errmsg'] = urldecode($errmsg);
    }

    // get an array of all the urls and labels for the search fields
    $data['searchfields'] = xarModAPIFunc('shopping', 'admin', 'getsearchfields',
                                          array('type' => 'recos',
                                                'catid' => $catid,
                                                'sort' => $sort,
                                                'sortorder' => $sortorder,
                                                'startnum' => $startnum,
                                                'level' => 'admin',
                                                'option1' => $option1,
                                                'option2' => $option2));

    // unset the url of the field we are currently searching
    unset($data['searchfields'][$search]['url']);

    // get an array of the headings for the table with urls to sort
    $data['headings'] = xarModAPIFunc('shopping', 'admin', 'getheads',
                                      array('type' => 'recos',
                                            'catid' => $catid,
                                            'sortorder' => $sortorder,
                                            'startnum' => $startnum,
                                            'search' => $search,
                                            'level' => 'admin',
                                            'option1' => $option1,
                                            'option2' => $option2));
    // set sortorder url
    $data['sortorderurl'] = xarModURL('shopping','admin','viewrecos',
                                        array('catid' => $catid,
                                              'sort' => $sort,
                                              'startnum' => $startnum,
                                              'search' => $search,
                                              'option1' => $option1,
                                              'option2' => $option2));

    // set urls for action
    $data['delurl'] = xarModURL('shopping', 'admin', 'deletereco',
                                  array('startnum' => $startnum,
                                        'catid' => $catid,
                                        'sort' => $sort,
                                        'sortorder' => $sortorder,
                                        'startnum' => $startnum,
                                        'search' => $search,
                                        'option1' => $option1,
                                        'option2' => $option2));
    if (xarSecurityCheck('ReadShoppingItems', 0)) {
      $data['dispurl'] = xarModURL('shopping','user','displayitem');
    }


    // set the number of items to return
    $numitems = xarModGetVar('shopping', 'recosperpage');

    // get the actual name of the field we are sorting by
    if ($sort == 1) {
      $orderby = 'xar_rid';
    } elseif ($sort == 2) {
      $orderby = 'xar_iid1';
    } elseif ($sort == 3) {
      $orderby = 'xar_iid2';
    } elseif ($sort == 4) {
      $orderby = 'xar_uname';
    }

    // set vars for search options
    $data['options']['url'] = xarModURL('shopping','admin','viewrecos',
                                        array('catid' => $catid,
                                              'sort' => $sort,
                                              'sortorder' => $sortorder,
                                              'search' => $search));
    $data['options']['submitlabel'] = xarML('Get Recos');

    switch ($search) {
      case 0:
        // get items based on search options
        $data['recos'] = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                       array('order' => array($orderby => $sortorder),
                                             'startnum' => $startnum,
                                             'catid' => $catid));
        $data['pager'] = xarTplGetPager($startnum,
                                        xarModAPIFunc('shopping', 'user', 'countrecos',
                                                      array('catid' => $catid)),
                                        xarModURL('shopping', 'admin', 'viewrecos',
                                                  array('startnum' => '%%',
                                                        'catid' => $catid,
                                                        'sort' => $sort,
                                                        'sortorder' => $sortorder,
                                                        'search' => $search,
                                                        'option1' => $option1,
                                                        'option2' => $option2)),
                                        $numitems);
        break;
      case 1:
        // set labels and values
        $data['option1']['label'] = xarML('Reco ID Is:');
        $data['option2']['label'] = xarML('Enter Reco ID:');
        if (isset($option1)) {
          $data['option1']['val'] = $option1;
        } else {
          $data['option1']['val'] = 0;
        }
        if (isset($option2)) {
          $option2 = trim($option2);
          $data['option2']['val'] = $option2;
        } else {
          $data['option2']['val'] = '';
        }

        // convert option1 value to what the getallrecos funciton can understand
        if ($option1 == 0) {
          $option1 = '=';
        } elseif ($option1 == 1) {
          $option1 = '<';
        } elseif ($option1 == 2) {
          $option1 = '>';
        } elseif ($option1 == 3) {
          $option1 = 'BETWEEN';
        }

        // if the user choose between for option1, we have to explode option2
        if ($option1 == 'BETWEEN' && isset($option2) && !empty($option2)) {
          if (!eregi('-', $option2)) {
              xarResponseRedirect(xarModURL('shopping','admin','viewrecos',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'startnum' => $startnum,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp; You must separate low and high values with a hyphen<br />")));
          } else {
            list($lowval, $highval) = explode('-', $option2);
            $lowval = trim($lowval);
            $highval = trim($highval);
          }
        }

        // check validity of user entry for option2
        if ($option1 != 'BETWEEN' && isset($option2) && !empty($option2)) {
          if (!is_numeric($option2)) {
            xarResponseRedirect(xarModURL('shopping','admin','viewrecos',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'startnum' => $startnum,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp;You must enter a numeric value for Reco ID<br />")));
          }
        } else {
          if ((isset($lowval) && !is_numeric($lowval)) || (isset($highval) && !is_numeric($highval))) {
            xarResponseRedirect(xarModURL('shopping','admin','viewrecos',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'startnum' => $startnum,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp;You must enter numeric values to search between<br />")));
          }
        }

        // get the actual name of the field we are searching on
        $wherewhat = 'xar_rid';

        // get items based on search options
        // check to see if option1 has been set
        // if it hasn't, we can't send a 'where' array to get
        if (isset($option2) && !empty($option2)) {
          // if option1 is not between then just pass option2 else we pass the high and low vals
          if ($option1 != 'BETWEEN') {
            $data['recos'] = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                           array('where' => array($wherewhat => array($option1 => $option2)),
                                                 'order' => array($orderby => $sortorder),
                                                 'startnum' => $startnum,
                                                 'catid' => $catid));
            $data['pager'] = xarTplGetPager($startnum,
                                            xarModAPIFunc('shopping', 'user', 'countrecos',
                                                          array('catid' => $catid,
                                                                'where' => array($wherewhat => array($option1 => $option2)))),
                                            xarModURL('shopping', 'admin', 'viewrecos',
                                                      array('startnum' => '%%',
                                                            'catid' => $catid,
                                                            'sort' => $sort,
                                                            'sortorder' => $sortorder,
                                                            'search' => $search,
                                                            'option1' => $option1,
                                                            'option2' => $option2)),
                                            $numitems);
          }  else {
            $data['recos'] = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                           array('where' => array($wherewhat => array($option1 => array($lowval, $highval))),
                                                 'order' => array($orderby => $sortorder),
                                                 'startnum' => $startnum,
                                                 'catid' => $catid));
            $data['pager'] = xarTplGetPager($startnum,
                                            xarModAPIFunc('shopping', 'user', 'countrecos',
                                                          array('catid' => $catid,
                                                                'where' => array($wherewhat => array($option1 => array($lowval, $highval))))),
                                            xarModURL('shopping', 'admin', 'viewrecos',
                                                      array('startnum' => '%%',
                                                            'catid' => $catid,
                                                            'sort' => $sort,
                                                            'sortorder' => $sortorder,
                                                            'search' => $search,
                                                            'option1' => $option1,
                                                            'option2' => $option2)),
                                            $numitems);
          }
        } else {
          $data['recos'] = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                         array('order' => array($orderby => $sortorder),
                                               'startnum' => $startnum,
                                               'catid' => $catid));
          $data['pager'] = xarTplGetPager($startnum,
                                          xarModAPIFunc('shopping', 'user', 'countrecos',
                                                        array('catid' => $catid)),
                                          xarModURL('shopping', 'admin', 'viewrecos',
                                                    array('startnum' => '%%',
                                                          'catid' => $catid,
                                                          'sort' => $sort,
                                                          'sortorder' => $sortorder,
                                                          'search' => $search,
                                                          'option1' => $option1,
                                                          'option2' => $option2)),
                                          $numitems);
        }

        break;

      case 2:
        // set labels and values
        $data['option1']['label'] = xarML('Item ID Is:');
        $data['option2']['label'] = xarML('Enter Item ID:');
        if (isset($option1)) {
          $data['option1']['val'] = $option1;
        } else {
          $data['option1']['val'] = 0;
        }
        if (isset($option2)) {
          $option2 = trim($option2);
          $data['option2']['val'] = $option2;
        } else {
          $data['option2']['val'] = '';
        }

        // convert option1 value to what the getallrecos funciton can understand
        if ($option1 == 0) {
          $option1 = '=';
        } elseif ($option1 == 1) {
          $option1 = '<';
        } elseif ($option1 == 2) {
          $option1 = '>';
        } elseif ($option1 == 3) {
          $option1 = 'BETWEEN';
        }

        // if the user choose between for option1, we have to explode option2
        if ($option1 == 'BETWEEN' && isset($option2) && !empty($option2)) {
          if (!eregi('-', $option2)) {
              xarResponseRedirect(xarModURL('shopping','admin','viewrecos',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'startnum' => $startnum,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp; You must separate low and high values with a hyphen<br />")));
          } else {
            list($lowval, $highval) = explode('-', $option2);
            $lowval = trim($lowval);
            $highval = trim($highval);
          }
        }

        // check validity of user entry for option2
        if ($option1 != 'BETWEEN' && isset($option2) && !empty($option2)) {
          if (!is_numeric($option2)) {
            xarResponseRedirect(xarModURL('shopping','admin','viewrecos',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'startnum' => $startnum,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp;You must enter a numeric value for Reco ID<br />")));
          }
        } else {
          if ((isset($lowval) && !is_numeric($lowval)) || (isset($highval) && !is_numeric($highval))) {
            xarResponseRedirect(xarModURL('shopping','admin','viewrecos',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'startnum' => $startnum,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp;You must enter numeric values to search between<br />")));
          }
        }

        // get items based on search options
        // check to see if option1 has been set
        // if it hasn't, we can't send a 'where' array to get
        if (isset($option2) && !empty($option2)) {
          // if option1 is not between then just pass option2 else we pass the high and low vals
          if ($option1 != 'BETWEEN') {
            $data['recos'] = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                           array('where' => array('xar_iid1' => array($option1 => $option2),
                                                                  'xar_iid2' => array($option1 => $option2)),
                                                 'order' => array($orderby => $sortorder),
                                                 'startnum' => $startnum,
                                                 'catid' => $catid));
            $data['pager'] = xarTplGetPager($startnum,
                                            xarModAPIFunc('shopping', 'user', 'countrecos',
                                                          array('catid' => $catid,
                                                                'where' => array('xar_iid1' => array($option1 => $option2),
                                                                                 'xar_iid2' => array($option1 => $option2)))),
                                            xarModURL('shopping', 'admin', 'viewrecos',
                                                      array('startnum' => '%%',
                                                            'catid' => $catid,
                                                            'sort' => $sort,
                                                            'sortorder' => $sortorder,
                                                            'search' => $search,
                                                            'option1' => $option1,
                                                            'option2' => $option2)),
                                            $numitems);
          }  else {
            $data['recos'] = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                           array('where' => array('xar_iid1' => array($option1 => array($lowval, $highval)),
                                                                  'xar_iid2' => array($option1 => array($lowval, $highval))),
                                                 'order' => array($orderby => $sortorder),
                                                 'startnum' => $startnum,
                                                 'catid' => $catid));
            $data['pager'] = xarTplGetPager($startnum,
                                            xarModAPIFunc('shopping', 'user', 'countrecos',
                                                          array('catid' => $catid,
                                                                'where' => array('xar_iid1' => array($option1 => array($lowval, $highval)),
                                                                                 'xar_iid2' => array($option1 => array($lowval, $highval))))),
                                            xarModURL('shopping', 'admin', 'viewrecos',
                                                      array('startnum' => '%%',
                                                            'catid' => $catid,
                                                            'sort' => $sort,
                                                            'sortorder' => $sortorder,
                                                            'search' => $search,
                                                            'option1' => $option1,
                                                            'option2' => $option2)),
                                            $numitems);
          }
        } else {
          $data['recos'] = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                         array('order' => array($orderby => $sortorder),
                                               'startnum' => $startnum,
                                               'catid' => $catid));
          $data['pager'] = xarTplGetPager($startnum,
                                          xarModAPIFunc('shopping', 'user', 'countrecos',
                                                        array('catid' => $catid)),
                                          xarModURL('shopping', 'admin', 'viewrecos',
                                                    array('startnum' => '%%',
                                                          'catid' => $catid,
                                                          'sort' => $sort,
                                                          'sortorder' => $sortorder,
                                                          'search' => $search,
                                                          'option1' => $option1,
                                                          'option2' => $option2)),
                                          $numitems);
        }

        break;

      case 3:
        // set labels and vals
        $data['option1']['label'] = xarML('Item Name:');
        $data['option2']['label'] = xarML('Enter Item Name:');
        if (isset($option1)) {
          $data['option1']['val'] = $option1;
        } else {
          $data['option1']['val'] = 0;
        }
        if (isset($option2)) {
          $data['option2']['val'] = $option2;
        } else {
          $data['option2']['val'] = '';
        }

        // in this case, we are using LIKE in the query, so we need to add the proper symbols
        // we are getting the results from the items table first
        if (!empty($option2)) {
          if ($option1 == 1) { // begins with
            $option2 = "$option2%";
          } elseif ($option1 == 2) {
            $option2 = "%$option2";  // end with
          } elseif ($option1 == 3) {
            $option2 = "%$option2%"; // contains
          }
        }

        // get items based on search options
        // check to see if option1 has been set
        // if it hasn't, we can't send a 'where' array to get
        if (isset($option2) && !empty($option2)) {
          // if option1 is not between then just pass option2 else we pass the high and low vals
          $items = xarModAPIFunc('shopping', 'user', 'getallitems',
                                         array('where' => array('xar_iname' => array("LIKE" => "'$option2'")),
                                               'order' => array('xar_iid' => 'ASC'),
                                               'startnum' => $startnum,
                                               'catid' => $catid));
          // get each item id retrieved into an array
          if (is_array($items)) {
            foreach($items as $item) {
              $itemids[] = $item['iid'];
            }
            // get the high and low ids
            $lowval = $itemids[0];
            $highval = $itemids[count($itemids) - 1];
          } else {
            $lowval = -1;
            $highval = -1;
          }

          // search between the low and high vals
            $data['recos'] = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                           array('where' => array('xar_iid1' => array('BETWEEN' => array($lowval, $highval)),
                                                                  'xar_iid2' => array('BETWEEN' => array($lowval, $highval))),
                                                 'order' => array($orderby => $sortorder),
                                                 'startnum' => $startnum,
                                                 'catid' => $catid));
            $data['pager'] = xarTplGetPager($startnum,
                                            xarModAPIFunc('shopping', 'user', 'countrecos',
                                                          array('catid' => $catid,
                                                                'where' => array('xar_iid1' => array('BETWEEN' => array($lowval, $highval)),
                                                                                 'xar_iid2' => array('BETWEEN' => array($lowval, $highval))))),
                                            xarModURL('shopping', 'admin', 'viewrecos',
                                                      array('startnum' => '%%',
                                                            'catid' => $catid,
                                                            'sort' => $sort,
                                                            'sortorder' => $sortorder,
                                                            'search' => $search,
                                                            'option1' => $option1,
                                                            'option2' => $option2)),
                                            $numitems);
        } else {
          $data['recos'] = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                         array('order' => array($orderby => $sortorder),
                                               'startnum' => $startnum,
                                               'catid' => $catid));
          $data['pager'] = xarTplGetPager($startnum,
                                          xarModAPIFunc('shopping', 'user', 'countrecos',
                                                        array('catid' => $catid)),
                                          xarModURL('shopping', 'admin', 'viewrecos',
                                                    array('startnum' => '%%',
                                                          'catid' => $catid,
                                                          'sort' => $sort,
                                                          'sortorder' => $sortorder,
                                                          'search' => $search,
                                                          'option1' => $option1,
                                                          'option2' => $option2)),
                                          $numitems);
        }
        break;

      case 4:
        // get all the users who have submitted a reco
        $users = xarModAPIFunc('shopping', 'user', 'getallrecos',
                               array('distinct' => true,
                                     'order' => array('xar_uname' => 'ASC')));
        if (is_array($users)) {
          foreach($users as $user) {
            $data['unames'][] = $user['uname'];
          }
        } else {
          $data['nonames'] = true;
        }

        // set labels and vals
        $data['option1']['label'] = xarML('Show Recos Submitted By:');
        if (isset($option1)) {
          $data['option1']['val'] = $option1;
        }

        // get items based on search options
        // check to see if options have been set
        // if it hasn't, we can't send a 'where' array to get
        if (isset($option1)) {
          $data['recos'] = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                         array('where' => array('xar_uname' => array('=' => "'$option1'")),
                                               'order' => array($orderby => $sortorder),
                                               'startnum' => $startnum,
                                               'catid' => $catid,
                                               'getratings' => true,
                                               'gethits' => true));
          $data['pager'] = xarTplGetPager($startnum,
                                          xarModAPIFunc('shopping', 'user', 'countrecos',
                                                        array('catid' => $catid,
                                                              'where' => array('xar_uname' => array('=' => "'$option1'")))),
                                          xarModURL('shopping', 'admin', 'viewrecos',
                                                    array('startnum' => '%%',
                                                          'catid' => $catid,
                                                          'sort' => $sort,
                                                          'sortorder' => $sortorder,
                                                          'search' => $search,
                                                          'option1' => $option1,
                                                          'option2' => $option2)),
                                          $numitems);
        } else {
          $data['recos'] = xarModAPIFunc('shopping', 'user', 'getallrecos',
                                         array('order' => array($orderby => $sortorder),
                                               'startnum' => $startnum,
                                               'catid' => $catid,
                                               'getratings' => true,
                                               'gethits' => true));
          $data['pager'] = xarTplGetPager($startnum,
                                          xarModAPIFunc('shopping', 'user', 'countrecos',
                                                        array('catid' => $catid)),
                                          xarModURL('shopping', 'admin', 'viewrecos',
                                                    array('startnum' => '%%',
                                                          'catid' => $catid,
                                                          'sort' => $sort,
                                                          'sortorder' => $sortorder,
                                                          'search' => $search,
                                                          'option1' => $option1,
                                                          'option2' => $option2)),
                                          $numitems);
        }
        break;
     }

    return $data;
}
?>