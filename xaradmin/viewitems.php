<?php
/*/
 * viewitems function
 * displays items based on filtering options
 * also acts as a search for items
 *
 * @returns template variables
 *
 * $search values: 0=all 1=iid 2=status 3=stock 4=name 5=description 6=date 7=price
 * $sort values: same as above, except 0 is not ever used
 * $sortorder: ASC or DESC
/*/
function shopping_admin_viewitems($args)
{
    // security check
    if (!xarSecurityCheck('EditShoppingItems')) return;

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
                                          array('type' => 'items',
                                                'catid' => $catid,
                                                'startnum' => $startnum,
                                                'sort' => $sort,
                                                'sortorder' => $sortorder,
                                                'level' => 'admin',
                                                'option1' => $option1,
                                                'option2' => $option2));

    // unset the url of the field we are currentlt searching
    unset($data['searchfields'][$search]['url']);

    // get an array of the headings for the table with urls to sort
    $data['headings'] = xarModAPIFunc('shopping', 'admin', 'getheads',
                                      array('type' => 'items',
                                            'catid' => $catid,
                                            'sortorder' => $sortorder,
                                            'startnum' => $startnum,
                                            'search' => $search,
                                            'level' => 'admin',
                                            'option1' => $option1,
                                            'option2' => $option2));
    // set sortorder url
    $data['sortorderurl'] = xarModURL('shopping','admin','viewitems',
                                        array('catid' => $catid,
                                              'sort' => $sort,
                                              'search' => $search,
                                              'startnum' => $startnum,
                                              'option1' => $option1,
                                              'option2' => $option2));

    // set urls for action
    if (xarSecurityCheck('DeleteShoppingItems', 0)){
      $data['delurl'] = xarModURL('shopping', 'admin', 'deleteitem',
                                  array('startnum' => $startnum,
                                        'catid' => $catid,
                                        'sort' => $sort,
                                        'sortorder' => $sortorder,
                                        'search' => $search,
                                        'option1' => $option1,
                                        'option2' => $option2));
    }
    if (xarSecurityCheck('EditShoppingItems', 0)) {
      $data['editurl'] = xarModURL('shopping','admin','edititem');
    }
    if (xarSecurityCheck('ReadShoppingItems', 0)) {
      $data['dispurl'] = xarModURL('shopping','user','displayitem');
      $data['picsurl'] = xarModURL('shopping','user','displayitem', array('phase' => 3));
    }
    if (xarSecurityCheck('ViewShoppingRecos', 0)) {
      $data['recosurl'] = xarModURL('shopping','user','displayitem', array('phase' => 4));
    }

    // sets urls for quicksearches
    $thismonth = date('n-Y');
    $data['thismonthQS'] = xarModURL('shopping','admin','viewitems',
                                     array('sort' => 3,
                                           'catid' => $catid,
                                           'startnum' => $startnum,
                                           'search' => '3',
                                           'option1' => '0',
                                           'option2' => $thismonth));
    $data['lowstockQS'] = xarModURL('shopping','admin','viewitems',
                                     array('sort' => 6,
                                           'catid' => $catid,
                                           'startnum' => $startnum,
                                           'search' => '5',
                                           'option1' => '1'));
    $data['backordQS'] = xarModURL('shopping','admin','viewitems',
                                     array('sort' => 6,
                                           'catid' => $catid,
                                           'startnum' => $startnum,
                                           'search' => '5',
                                           'option1' => '2'));

    // set the number of items to return
    $numitems = xarModGetVar('shopping', 'itemsperpage');

    // get the actual name of the field we are sorting by
    if ($sort == 1) {
      $orderby = 'xar_iid';
    } elseif ($sort == 2) {
      $orderby = 'xar_iname';
    } elseif ($sort == 3) {
      $orderby = 'xar_idate';
    } elseif ($sort == 4) {
      $orderby = 'xar_iprice';
    } elseif ($sort == 5) {
      $orderby = 'xar_istatus';
    } elseif ($sort == 6) {
      $orderby = 'xar_istock';
    } elseif ($sort == 7) {
      $orderby = 'xar_ibuys';
    } elseif ($sort == 8) {
      $orderby = 'xar_rating';
    } elseif ($sort == 9) {
      $orderby = 'xar_hits';
    }

    // set vars for search options
    $data['options']['url'] = xarModURL('shopping','admin','viewitems',
                                        array('catid' => $catid,
                                              'sort' => $search,
                                              'sortorder' => $sortorder,
                                              'search' => $search));
    $data['options']['submitlabel'] = xarML('Get Items');

    switch ($search) {
      case 0:
        // get items based on search options
        $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                       array('order' => array($orderby => $sortorder),
                                             'startnum' => $startnum,
                                             'catid' => $catid,
                                             'getratings' => true,
                                             'gethits' => true));
        $data['pager'] = xarTplGetPager($startnum,
                                        xarModAPIFunc('shopping', 'user', 'countitems',
                                                      array('catid' => $catid)),
                                        xarModURL('shopping', 'admin', 'viewitems',
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

        // convert option1 value to what the getallitems funciton can understand
        if ($option1 == 0) {
          $action = '=';
        } elseif ($option1 == 1) {
          $action = '<';
        } elseif ($option1 == 2) {
          $action = '>';
        } elseif ($option1 == 3) {
          $action = 'BETWEEN';
        }

        // if the user choose between for option1, we have to explode option2
        if ($action == 'BETWEEN' && isset($option2) && !empty($option2)) {
          if (!eregi('-', $option2)) {
              xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp; You must separate low and high values with a hyphen<br />")));
          } else {
            list($lowval, $highval) = explode('-', $option2);
            $lowval = trim($lowval);
            $highval = trim($highval);
          }
        }

        // check validity of user entry for option2
        if ($action != 'BETWEEN' && isset($option2) && !empty($option2)) {
          if (!is_numeric($option2)) {
            xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp;You must enter a numeric value for Item ID<br />")));
          }
        } else {
          if ((isset($lowval) && !is_numeric($lowval)) || (isset($highval) && !is_numeric($highval))) {
            xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp;You must enter numeric values to search between<br />")));
          }
        }

        // get the actual name of the field we are searching on
        $wherewhat = 'xar_iid';

        // get items based on search options
        // check to see if option1 has been set
        // if it hasn't, we can't send a 'where' array to get
        if (isset($option2) && !empty($option2)) {
          // if option1 is not between then just pass option2 else we pass the high and low vals
          if ($action != 'BETWEEN') {
            $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                           array('where' => array($wherewhat => array($action => $option2)),
                                                 'order' => array($orderby => $sortorder),
                                                 'startnum' => $startnum,
                                                 'catid' => $catid,
                                                 'getratings' => true,
                                                 'gethits' => true));
            $data['pager'] = xarTplGetPager($startnum,
                                            xarModAPIFunc('shopping', 'user', 'countitems',
                                                          array('catid' => $catid,
                                                                'where' => array($wherewhat => array($action => $option2)))),
                                            xarModURL('shopping', 'admin', 'viewitems',
                                                      array('startnum' => '%%',
                                                            'catid' => $catid,
                                                            'sort' => $sort,
                                                            'sortorder' => $sortorder,
                                                            'search' => $search,
                                                            'option1' => $option1,
                                                            'option2' => $option2)),
                                            $numitems);
          }  else {
            $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                           array('where' => array($wherewhat => array($action => array($lowval, $highval))),
                                                 'order' => array($orderby => $sortorder),
                                                 'startnum' => $startnum,
                                                 'catid' => $catid,
                                                 'getratings' => true,
                                                 'gethits' => true));
            $data['pager'] = xarTplGetPager($startnum,
                                            xarModAPIFunc('shopping', 'user', 'countitems',
                                                          array('catid' => $catid,
                                                                'where' => array($wherewhat => array($action => array($lowval, $highval))))),
                                            xarModURL('shopping', 'admin', 'viewitems',
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
          $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                         array('order' => array($orderby => $sortorder),
                                               'startnum' => $startnum,
                                               'catid' => $catid,
                                               'getratings' => true,
                                               'gethits' => true));
          $data['pager'] = xarTplGetPager($startnum,
                                          xarModAPIFunc('shopping', 'user', 'countitems',
                                                        array('catid' => $catid)),
                                          xarModURL('shopping', 'admin', 'viewitems',
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
        if (!empty($option2)) {
          if ($option1 == 1) { // begins with
            $qstr = "$option2%";
          } elseif ($option1 == 2) {
            $qstr = "%$option2";  // end with
          } elseif ($option1 == 3) {
            $qstr = "%$option2%"; // contains
          }
        }

        // get the actual name of the field we are searching on
        $wherewhat = 'xar_iname';

        // get items based on search options
        // check to see if option1 has been set
        // if it hasn't, we can't send a 'where' array to get
        if (isset($option2) && !empty($option2)) {
          // if option1 is not between then just pass option2 else we pass the high and low vals
          $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                         array('where' => array($wherewhat => array("LIKE" => "'$qstr'")),
                                               'order' => array($orderby => $sortorder),
                                               'startnum' => $startnum,
                                               'catid' => $catid,
                                               'getratings' => true,
                                               'gethits' => true));
          $data['pager'] = xarTplGetPager($startnum,
                                          xarModAPIFunc('shopping', 'user', 'countitems',
                                                        array('catid' => $catid,
                                                              'where' => array($wherewhat => array("LIKE" => "'$qstr'")))),
                                          xarModURL('shopping', 'admin', 'viewitems',
                                                    array('startnum' => '%%',
                                                          'catid' => $catid,
                                                          'sort' => $sort,
                                                          'sortorder' => $sortorder,
                                                          'search' => $search,
                                                          'option1' => $option1,
                                                          'option2' => $option2)),
                                          $numitems);
        } else {
          $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                         array('order' => array($orderby => $sortorder),
                                               'startnum' => $startnum,
                                               'catid' => $catid,
                                               'getratings' => true,
                                               'gethits' => true));
          $data['pager'] = xarTplGetPager($startnum,
                                          xarModAPIFunc('shopping', 'user', 'countitems',
                                                        array('catid' => $catid)),
                                          xarModURL('shopping', 'admin', 'viewitems',
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
        $data['option1']['label'] = xarML('Date Is:');
        $data['option2']['label'] = xarML('Enter Date:');
        if (isset($option1)) {
          $data['option1']['val'] = $option1;
        } else {
          $option1 = 0;
          $data['option1']['val'] = 0;
        }
        if (isset($option2)) {
          $data['option2']['val'] = $option2;
        } else {
          $data['option2']['val'] = '';
        }

        // convert option1 value to what the getallitems funciton can understand
        if ($option1 == 0) {
          $action = '=';
        } elseif ($option1 == 1) {
          $action = '<';
        } elseif ($option1 == 2) {
          $action = '>';
        } elseif ($option1 == 3) {
          $action = 'BETWEEN';
        }

        // trim whitespace from option2
        $option2 = eregi_replace('[[:space:]]', '', $option2);

        // if the user choose between for option1, we have to explode option2
        if ($action == 'BETWEEN' && isset($option2) && !empty($option2)) {
          if (!eregi('\|', $option2)) {  // check for pipe separting the low and high values
              xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp; You must separate low and high values with a pipe<br />")));
          } else {
            // get the low and high values
            list($lowval, $highval) = explode('|', $option2);

            // check the low value for formating
            if (eregi('^[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{2,4}$',$lowval)) {
              // there is mm-dd-yy or mm-dd-yyyy we explode as seen
              list($lmonth, $lday, $lyear) = explode('-', $lowval);
            } elseif (eregi('^[0-9]{1,2}\-[0-9]{2,4}$', $lowval)) {
              // if we have mm-yy or mm-yyyy
              list($lmonth, $lyear) = explode('-', $lowval);
            } else {
              // if there formating is not correct send an error and redirect
              xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp; You must enter dates in m[-d]-yy format<br />")));
            }

                // check the validity of the lowval
                $lday = isset($lday)?$lday:1;
                if (!checkdate($lmonth, $lday, $lyear)) {
                  xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                                array('catid' => $catid,
                                                      'sort' => $sort,
                                                      'sortorder' => $sortorder,
                                                      'search' => $search,
                                                      'errmsg' => "&bull;&nbsp; The start date you entered does not exist.  You must enter a valid date<br />")));
                }
                // convert to unix timestamp
                $startdate = mktime(0,0,0,$lmonth, $lday, $lyear);


            // check the high value for formating
            if (eregi('^[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{2,4}$',$highval)) {
              list($hmonth, $hday, $hyear) = explode('-', $highval);
            } elseif (eregi('^[0-9]{1,2}\-[0-9]{2,4}$', $highval)) {
              list($hmonth, $hyear) = explode('-', $highval);
            } else {
              xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp; You must enter dates in m[-d]-yy format<br />")));
            }

                // check the validity of the highval
                if (!isset($hday)) {
                  for ($i=31; $i >= 28; $i--) {
                    if (checkdate($hmonth, $i, $hyear)) {
                      $hday = $i;
                      break;
                    }
                  }
                }

                if (!checkdate($hmonth, $hday, $hyear)) {
                  xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                                array('catid' => $catid,
                                                      'sort' => $sort,
                                                      'sortorder' => $sortorder,
                                                      'search' => $search,
                                                      'errmsg' => "&bull;&nbsp; The end date you entered does not exist.  You must enter a valid date<br />")));
                }
                // convert to unix timestamp
                $enddate = mktime(0,0,0,$hmonth, $hday, $hyear);
          }
        } elseif (isset($option2) && !empty($option2)) { // Option1 is not 'between'
          if (eregi('^[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{2,4}$',$option2)) {
            list($month, $day, $year) = explode('-', $option2);
          } elseif (eregi('^[0-9]{1,2}\-[0-9]{2,4}$', $option2)) {
            list($month, $year) = explode('-', $option2);
          } else {
            xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp; You must enter dates in m[-d]-yy format<br />")));
          }

                // check the validity of the option2
                if (!isset($day)) {
                  if ($action == '=') {
                      $action = 'BETWEEN';
                      $startdate = mktime(0,0,0,$month, 1, $year);
                      for ($i=31; $i >= 28; $i--) {
                        if (checkdate($month, $i, $year)) {
                          $day = $i;
                          break;
                        }
                      }
                      $enddate = mktime(0,0,0,$month, $day, $year);
                  } elseif ($action == '>') {
                      for ($i=31; $i >= 28; $i--) {
                        if (checkdate($month, $i, $year)) {
                          $day = $i;
                          break;
                        }
                      }
                      $datetofind = mktime(0,0,0,$month, $day, $year);
                  } elseif ($action == '<') {
                    $day = 1;
                    $datetofind = mktime(0,0,0,$month, $day, $year);
                  }
                } else {
                      if (!checkdate($month, $day, $year)) {
                        xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                                      array('catid' => $catid,
                                                            'sort' => $sort,
                                                            'sortorder' => $sortorder,
                                                            'search' => $search,
                                                            'errmsg' => "&bull;&nbsp; The date you entered does not exist.  You must enter a valid date<br />")));
                      }
                      // convert to unix timestamp
                      $datetofind = mktime(0,0,0,$month, $day, $year);
                }
        }

        // get the actual name of the field we are searching on
        $wherewhat = 'UNIX_TIMESTAMP(xar_idate)';

        // get items based on search options
        // check to see if options have been set
        // if it hasn't, we can't send a 'where' array to get
        if (isset($option2) && !empty($option2)) {
          if ($action != 'BETWEEN') {
            $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                           array('where' => array($wherewhat => array($action => $datetofind)),
                                                 'order' => array($orderby => $sortorder),
                                                 'startnum' => $startnum,
                                                 'catid' => $catid,
                                                 'getratings' => true,
                                                 'gethits' => true));
            $data['pager'] = xarTplGetPager($startnum,
                                            xarModAPIFunc('shopping', 'user', 'countitems',
                                                          array('catid' => $catid,
                                                                'where' => array($wherewhat => array($action => $datetofind)))),
                                            xarModURL('shopping', 'admin', 'viewitems',
                                                      array('startnum' => '%%',
                                                            'catid' => $catid,
                                                            'sort' => $sort,
                                                            'sortorder' => $sortorder,
                                                            'search' => $search,
                                                            'option1' => $option1,
                                                            'option2' => $option2)),
                                            $numitems);
          } else {
            $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                           array('where' => array($wherewhat => array($action => array($startdate, $enddate))),
                                                 'order' => array($orderby => $sortorder),
                                                 'startnum' => $startnum,
                                                 'catid' => $catid,
                                                 'getratings' => true,
                                                 'gethits' => true));
            $data['pager'] = xarTplGetPager($startnum,
                                            xarModAPIFunc('shopping', 'user', 'countitems',
                                                          array('catid' => $catid,
                                                                'where' => array($wherewhat => array($action => array($startdate, $enddate))))),
                                            xarModURL('shopping', 'admin', 'viewitems',
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
          $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                         array('order' => array($orderby => $sortorder),
                                               'startnum' => $startnum,
                                               'catid' => $catid,
                                               'getratings' => true,
                                               'gethits' => true));
          $data['pager'] = xarTplGetPager($startnum,
                                          xarModAPIFunc('shopping', 'user', 'countitems',
                                                        array('catid' => $catid)),
                                          xarModURL('shopping', 'admin', 'viewitems',
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
        // set labels and vals
        $data['option1']['label'] = xarML('Price Is:');
        $data['option2']['label'] = xarML('Enter Price:');
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

        // convert option1 value to what the getallitems funciton can understand
        if ($option1 == 0) {
          $action = '=';
        } elseif ($option1 == 1) {
          $action = '<';
        } elseif ($option1 == 2) {
          $action = '>';
        } elseif ($option1 == 3) {
          $action = 'BETWEEN';
        }

        // if the user choose between for option1, we have to explode option2
        if ($action == 'BETWEEN' && isset($option2) && !empty($option2)) {
          if (!eregi('-', $option2)) { // must have hypen for between
            xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp;You must separate low and high prices with a hyphen<br />")));
          } else {
            // get the low and high values
            list($lowval, $highval) = explode('-', $option2);
            $lowval = trim($lowval);
            $highval = trim($highval);
             // remove any entered dollar sign or commas
             $lowval = eregi_replace('\$', '', $lowval);
             $lowval = eregi_replace('\,', '', $lowval);
             $highval = eregi_replace('\$', '', $highval);
             $highval = eregi_replace('\,', '', $highval);

               // check validity of high and low vals
               if (!is_numeric($lowval) || !is_numeric($highval)) {
                 xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                               array('catid' => $catid,
                                                     'sort' => $sort,
                                                     'sortorder' => $sortorder,
                                                     'search' => $search,
                                                     'errmsg' => "&bull;&nbsp;You must enter valid prices<br />")));
               }
          }
        } elseif (isset($option2) && !empty($option2)) { // between not chosen
          // remove $ and , from input
          $option2 = eregi_replace('\$', '', $option2);
          $option2 = eregi_replace('\,', '', $option2);

            // check validity of input
            if (!is_numeric($option2)) {
              xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                            array('catid' => $catid,
                                                  'sort' => $sort,
                                                  'sortorder' => $sortorder,
                                                  'search' => $search,
                                                  'errmsg' => "&bull;&nbsp;You must enter a valid price<br />")));
            }
        }

        // get the actual name of the field we are searching on
        $wherewhat = 'xar_iprice';

        // get items based on search options
        // check to see if options have been set
        // if it hasn't, we can't send a 'where' array to get
        if (isset($option2) && !empty($option2)) {
          if ($action != 'BETWEEN') {
            $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                           array('where' => array($wherewhat => array($action => $option2)),
                                                 'order' => array($orderby => $sortorder),
                                                 'startnum' => $startnum,
                                                 'catid' => $catid,
                                                 'getratings' => true,
                                                 'gethits' => true));
            $data['pager'] = xarTplGetPager($startnum,
                                            xarModAPIFunc('shopping', 'user', 'countitems',
                                                          array('catid' => $catid,
                                                                'where' => array($wherewhat => array($action => $option2)))),
                                            xarModURL('shopping', 'admin', 'viewitems',
                                                      array('startnum' => '%%',
                                                            'catid' => $catid,
                                                            'sort' => $sort,
                                                            'sortorder' => $sortorder,
                                                            'search' => $search,
                                                            'option1' => $option1,
                                                            'option2' => $option2)),
                                            $numitems);
          } else {
            $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                           array('where' => array($wherewhat => array($action => array($lowval, $highval))),
                                                 'order' => array($orderby => $sortorder),
                                                 'startnum' => $startnum,
                                                 'catid' => $catid,
                                                 'getratings' => true,
                                                 'gethits' => true));
            $data['pager'] = xarTplGetPager($startnum,
                                            xarModAPIFunc('shopping', 'user', 'countitems',
                                                          array('catid' => $catid,
                                                                'where' => array($wherewhat => array($action => array($lowval, $highval))))),
                                            xarModURL('shopping', 'admin', 'viewitems',
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
          $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                         array('order' => array($orderby => $sortorder),
                                               'startnum' => $startnum,
                                               'catid' => $catid,
                                               'getratings' => true,
                                               'gethits' => true));
          $data['pager'] = xarTplGetPager($startnum,
                                          xarModAPIFunc('shopping', 'user', 'countitems',
                                                        array('catid' => $catid)),
                                          xarModURL('shopping', 'admin', 'viewitems',
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

      case 5:
        // set labels and vals
        $data['option1']['label'] = xarML('Show Items That Are:');
        if (isset($option1)) {
          $data['option1']['val'] = $option1;
        }

        // get the actual name of the field we are searching on
        $wherewhat = 'xar_istatus';

        // get items based on search options
        // check to see if options have been set
        // if it hasn't, we can't send a 'where' array to get
        if (isset($option1) && is_numeric($option1)) {
          $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                         array('where' => array($wherewhat => array('=' => $option1)),
                                               'order' => array($orderby => $sortorder),
                                               'startnum' => $startnum,
                                               'catid' => $catid,
                                               'getratings' => true,
                                               'gethits' => true));
          $data['pager'] = xarTplGetPager($startnum,
                                          xarModAPIFunc('shopping', 'user', 'countitems',
                                                        array('catid' => $catid,
                                                              'where' => array($wherewhat => array('=' => $option1)))),
                                          xarModURL('shopping', 'admin', 'viewitems',
                                                    array('startnum' => '%%',
                                                          'catid' => $catid,
                                                          'sort' => $sort,
                                                          'sortorder' => $sortorder,
                                                          'search' => $search,
                                                          'option1' => $option1,
                                                          'option2' => $option2)),
                                          $numitems);
        } else {
          $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                         array('order' => array($orderby => $sortorder),
                                               'startnum' => $startnum,
                                               'catid' => $catid,
                                               'getratings' => true,
                                               'gethits' => true));
          $data['pager'] = xarTplGetPager($startnum,
                                          xarModAPIFunc('shopping', 'user', 'countitems',
                                                        array('catid' => $catid)),
                                          xarModURL('shopping', 'admin', 'viewitems',
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

      case 6:
        // set labels and vals
        $data['option1']['label'] = xarML('Stock Level Is:');
        $data['option2']['label'] = xarML('Enter Stock Level:');
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

        // convert option1 value to what the getallitems funciton can understand
        if ($option1 == 0) {
          $action = '=';
        } elseif ($option1 == 1) {
          $action = '<';
        } elseif ($option1 == 2) {
          $action = '>';
        } elseif ($option1 == 3) {
          $action = 'BETWEEN';
        }

        // if the user choose between for option1, we have to explode option2
        if ($action == 'BETWEEN' && isset($option2) && !empty($option2)) {
          if (!eregi('-', $option2)) {
              xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp; You must separate low and high values with a hyphen<br />")));
          } else {
            list($lowval, $highval) = explode('-', $option2);
            $lowval = trim($lowval);
            $highval = trim($highval);
          }
        }

        // check validity of user entry for option2
        if ($action != 'BETWEEN' && isset($option2) && !empty($option2)) {
          if (!is_numeric($option2)) {
            xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp;You must enter a numeric value for Stock Level<br />")));
          }
        } else {
          if ((isset($lowval) && !is_numeric($lowval)) || (isset($highval) && !is_numeric($highval))) {
            xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp;You must enter numeric values to search between<br />")));
          }
        }

        // get the actual name of the field we are searching on
        $wherewhat = 'xar_istock';

        // get items based on search options
        // check to see if options have been set
        // if it hasn't, we can't send a 'where' array to get
        if (isset($option2) && !empty($option2)) {
          if ($action != 'BETWEEN') {
            $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                           array('where' => array($wherewhat => array($action => $option2)),
                                                 'order' => array($orderby => $sortorder),
                                                 'startnum' => $startnum,
                                                 'catid' => $catid,
                                                 'getratings' => true,
                                                 'gethits' => true));
            $data['pager'] = xarTplGetPager($startnum,
                                            xarModAPIFunc('shopping', 'user', 'countitems',
                                                          array('catid' => $catid,
                                                                'where' => array($wherewhat => array($action => $option2)))),
                                            xarModURL('shopping', 'admin', 'viewitems',
                                                      array('startnum' => '%%',
                                                            'catid' => $catid,
                                                            'sort' => $sort,
                                                            'sortorder' => $sortorder,
                                                            'search' => $search,
                                                            'option1' => $option1,
                                                            'option2' => $option2)),
                                            $numitems);
          } else {
            $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                           array('where' => array($wherewhat => array($action => array($lowval, $highval))),
                                                 'order' => array($orderby => $sortorder),
                                                 'startnum' => $startnum,
                                                 'catid' => $catid,
                                                 'getratings' => true,
                                                 'gethits' => true));
            $data['pager'] = xarTplGetPager($startnum,
                                            xarModAPIFunc('shopping', 'user', 'countitems',
                                                          array('catid' => $catid,
                                                                'where' => array($wherewhat => array($action => array($lowval, $highval))))),
                                            xarModURL('shopping', 'admin', 'viewitems',
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
          $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                         array('order' => array($orderby => $sortorder),
                                               'startnum' => $startnum,
                                               'catid' => $catid,
                                               'getratings' => true,
                                               'gethits' => true));
          $data['pager'] = xarTplGetPager($startnum,
                                          xarModAPIFunc('shopping', 'user', 'countitems',
                                                        array('catid' => $catid)),
                                          xarModURL('shopping', 'admin', 'viewitems',
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

      case 7:
        // set labels and vals
        $data['option1']['label'] = xarML('Times Bought Is:');
        $data['option2']['label'] = xarML('Enter Times Bought:');
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

        // convert option1 value to what the getallitems funciton can understand
        if ($option1 == 0) {
          $action = '=';
        } elseif ($option1 == 1) {
          $action = '<';
        } elseif ($option1 == 2) {
          $action = '>';
        } elseif ($option1 == 3) {
          $action = 'BETWEEN';
        }

        // if the user choose between for option1, we have to explode option2
        if ($action == 'BETWEEN' && isset($option2) && !empty($option2)) {
          if (!eregi('-', $option2)) {
              xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp; You must separate low and high values with a hyphen<br />")));
          } else {
            list($lowval, $highval) = explode('-', $option2);
            $lowval = trim($lowval);
            $highval = trim($highval);
          }
        }

        // check validity of user entry for option2
        if ($action != 'BETWEEN' && isset($option2) && !empty($option2)) {
          if (!is_numeric($option2)) {
            xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp;You must enter a numeric value for Times Bought<br />")));
          }
        } else {
          if ((isset($lowval) && !is_numeric($lowval)) || (isset($highval) && !is_numeric($highval))) {
            xarResponseRedirect(xarModURL('shopping','admin','viewitems',
                                           array('catid' => $catid,
                                                 'sort' => $sort,
                                                 'sortorder' => $sortorder,
                                                 'search' => $search,
                                                 'errmsg' => "&bull;&nbsp;You must enter numeric values to search between<br />")));
          }
        }

        // get the actual name of the field we are searching on
        $wherewhat = 'xar_ibuys';

        // get items based on search options
        // check to see if options have been set
        // if it hasn't, we can't send a 'where' array to get
        if (isset($option2) && !empty($option2)) {
          if ($action != 'BETWEEN') {
            $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                           array('where' => array($wherewhat => array($action => $option2)),
                                                 'order' => array($orderby => $sortorder),
                                                 'startnum' => $startnum,
                                                 'catid' => $catid,
                                                 'getratings' => true,
                                                 'gethits' => true));
            $data['pager'] = xarTplGetPager($startnum,
                                            xarModAPIFunc('shopping', 'user', 'countitems',
                                                          array('catid' => $catid,
                                                                'where' => array($wherewhat => array($action => $option2)))),
                                            xarModURL('shopping', 'admin', 'viewitems',
                                                      array('startnum' => '%%',
                                                            'catid' => $catid,
                                                            'sort' => $sort,
                                                            'sortorder' => $sortorder,
                                                            'search' => $search,
                                                            'option1' => $option1,
                                                            'option2' => $option2)),
                                            $numitems);
          } else {
            $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                           array('where' => array($wherewhat => array($action => array($lowval, $highval))),
                                                 'order' => array($orderby => $sortorder),
                                                 'startnum' => $startnum,
                                                 'catid' => $catid,
                                                 'getratings' => true,
                                                 'gethits' => true));
            $data['pager'] = xarTplGetPager($startnum,
                                            xarModAPIFunc('shopping', 'user', 'countitems',
                                                          array('catid' => $catid,
                                                                'where' => array($wherewhat => array($action => array($lowval, $highval))))),
                                            xarModURL('shopping', 'admin', 'viewitems',
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
          $data['items'] = xarModAPIFunc('shopping', 'user', 'getallitems',
                                         array('order' => array($orderby => $sortorder),
                                               'startnum' => $startnum,
                                               'catid' => $catid,
                                               'getratings' => true,
                                               'gethits' => true));
          $data['pager'] = xarTplGetPager($startnum,
                                          xarModAPIFunc('shopping', 'user', 'countitems',
                                                        array('catid' => $catid)),
                                          xarModURL('shopping', 'admin', 'viewitems',
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