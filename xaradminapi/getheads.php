<?php

/*/
 * getheads function
 *
 * @returns an array of headings to use for viewing
 * @param $type -- items, orders, recos, or profiles
 * @param $level -- admin or user
 * @param $sortorder -- the current sort order
 * @param $catid -- the category we are in currently
 * @param $search -- the current search field
 * @param $option1, $option2 -- search options
/*/
function shopping_adminapi_getheads($args)
{
    extract($args);

    if (!isset($type) || !isset($level) || !isset($sortorder) || !isset($search)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in #(4) module',
                     'parameter', 'user', 'getsearchfields',
                     'Shopping');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    $heads = array();

    switch ($type) {
      case 'items':
        $heads[0] = array('label' => xarML('ID'),
                          'sortval' => 1,
                          'url' => xarModURL('shopping', $level, 'viewitems',
                                             array('sort' => 1,
                                                   'sortorder' => $sortorder,
                                                   'startnum' => $startnum,
                                                   'catid' => $catid,
                                                   'search' => $search,
                                                   'option1' => $option1,
                                                   'option2' => $option2)));
        $heads[1] = array('label' => xarML('Name'),
                          'sortval' => 2,
                          'url' => xarModURL('shopping', $level, 'viewitems',
                                             array('sort' => 2,
                                                   'sortorder' => $sortorder,
                                                   'startnum' => $startnum,
                                                   'catid' => $catid,
                                                   'search' => $search,
                                                   'option1' => $option1,
                                                   'option2' => $option2)));
        $heads[2] = array('label' => xarML('Date'),
                          'sortval' => 3,
                          'url' => xarModURL('shopping', $level, 'viewitems',
                                             array('sort' => 3,
                                                   'sortorder' => $sortorder,
                                                   'startnum' => $startnum,
                                                   'catid' => $catid,
                                                   'search' => $search,
                                                   'option1' => $option1,
                                                   'option2' => $option2)));
        $heads[3] = array('label' => xarML('Price'),
                          'sortval' => 4,
                          'url' => xarModURL('shopping', $level, 'viewitems',
                                             array('sort' => 4,
                                                   'sortorder' => $sortorder,
                                                   'startnum' => $startnum,
                                                   'catid' => $catid,
                                                   'search' => $search,
                                                   'option1' => $option1,
                                                   'option2' => $option2)));
        $heads[4] = array('label' => xarML('Status'),
                          'sortval' => 5,
                          'url' => xarModURL('shopping', $level, 'viewitems',
                                             array('sort' => 5,
                                                   'sortorder' => $sortorder,
                                                   'startnum' => $startnum,
                                                   'catid' => $catid,
                                                   'search' => $search,
                                                   'option1' => $option1,
                                                   'option2' => $option2)));
        $heads[5] = array('label' => xarML('Stock'),
                          'sortval' => 6,
                          'url' => xarModURL('shopping', $level, 'viewitems',
                                             array('sort' => 6,
                                                   'sortorder' => $sortorder,
                                                   'startnum' => $startnum,
                                                   'catid' => $catid,
                                                   'search' => $search,
                                                   'option1' => $option1,
                                                   'option2' => $option2)));
        $heads[6] = array('label' => xarML('Buys'),
                          'sortval' => 7,
                          'url' => xarModURL('shopping', $level, 'viewitems',
                                             array('sort' => 7,
                                                   'sortorder' => $sortorder,
                                                   'startnum' => $startnum,
                                                   'catid' => $catid,
                                                   'search' => $search,
                                                   'option1' => $option1,
                                                   'option2' => $option2)));
        if (xarModIsHooked('ratings', 'shopping')) {
          $heads[7] = array('label' => xarML('Rating'),
                            'sortval' => 8,
                            'url' => xarModURL('shopping', $level, 'viewitems',
                                               array('sort' => 8,
                                                     'sortorder' => $sortorder,
                                                     'startnum' => $startnum,
                                                     'catid' => $catid,
                                                     'search' => $search,
                                                     'option1' => $option1,
                                                     'option2' => $option2)));
        }
        if (xarModIsHooked('hitcount', 'shopping')) {
          $heads[8] = array('label' => xarML('Views'),
                            'sortval' => 9,
                            'url' => xarModURL('shopping', $level, 'viewitems',
                                               array('sort' => 9,
                                                     'sortorder' => $sortorder,
                                                     'startnum' => $startnum,
                                                     'catid' => $catid,
                                                     'search' => $search,
                                                     'option1' => $option1,
                                                     'option2' => $option2)));
        }

        break;

      case 'recos':
        $heads[0] = array('label' => xarML('Reco ID'),
                          'sortval' => 1,
                          'url' => xarModURL('shopping', $level, 'viewrecos',
                                             array('sort' => 1,
                                                   'sortorder' => $sortorder,
                                                   'startnum' => $startnum,
                                                   'catid' => $catid,
                                                   'search' => $search,
                                                   'option1' => $option1,
                                                   'option2' => $option2)));
        $heads[1] = array('label' => xarML('Item 1'),
                          'sortval' => 2,
                          'url' => xarModURL('shopping', $level, 'viewrecos',
                                             array('sort' => 2,
                                                   'sortorder' => $sortorder,
                                                   'startnum' => $startnum,
                                                   'catid' => $catid,
                                                   'search' => $search,
                                                   'option1' => $option1,
                                                   'option2' => $option2)));
        $heads[2] = array('label' => xarML('Item 2'),
                          'sortval' => 3,
                          'url' => xarModURL('shopping', $level, 'viewrecos',
                                             array('sort' => 3,
                                                   'sortorder' => $sortorder,
                                                   'startnum' => $startnum,
                                                   'catid' => $catid,
                                                   'search' => $search,
                                                   'option1' => $option1,
                                                   'option2' => $option2)));
        $heads[3] = array('label' => xarML('Submitted By'),
                          'sortval' => 4,
                          'url' => xarModURL('shopping', $level, 'viewrecos',
                                             array('sort' => 4,
                                                   'sortorder' => $sortorder,
                                                   'startnum' => $startnum,
                                                   'catid' => $catid,
                                                   'search' => $search,
                                                   'option1' => $option1,
                                                   'option2' => $option2)));
        break;
    }

    return $heads;
}
?>
