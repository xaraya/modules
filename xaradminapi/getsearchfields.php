<?php

/*/
 * getsearchfields function
 *
 * @returns an array of fields to search on
 * @param $type -- items, orders, recos, or profiles
 * @param $sort -- the column we are sorting on
 * @param $sortorder -- the current sort order
 * @param $catid -- the category we are in currently
 * @param $level -- admin or user
/*/
function shopping_adminapi_getsearchfields($args)
{
    extract($args);

    // check parameters
    if (!isset($type) || !isset($sort) || !isset($sortorder) || !isset($level)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in #(4) module',
                     'parameter', 'user', 'getsearchfields',
                     'Shopping');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    $fields = array();

    switch ($type) {
      case 'items':
        $fields[0] = array('url' => xarModURL('shopping', $level,'viewitems',
                                              array('sort' => $sort,
                                                    'sortorder' => $sortorder,
                                                    'catid' => $catid,
                                                    'startnum' => $startnum)),
                           'label' => xarML('All'));
        $fields[1] = array('url' => xarModURL('shopping', $level,'viewitems',
                                              array('search' => 1,
                                                    'sort' => $sort,
                                                    'sortorder' => $sortorder,
                                                    'catid' => $catid,
                                                    'startnum' => $startnum)),
                           'label' => xarML('ID'));
        $fields[2] = array('url' => xarModURL('shopping',$level,'viewitems',
                                              array('search' => 2,
                                                    'sort' => $sort,
                                                    'sortorder' => $sortorder,
                                                    'catid' => $catid,
                                                    'startnum' => $startnum)),
                           'label' => xarML('Name'));
        $fields[3] = array('url' => xarModURL('shopping',$level,'viewitems',
                                              array('search' => 3,
                                                    'sort' => $sort,
                                                    'sortorder' => $sortorder,
                                                    'catid' => $catid,
                                                    'startnum' => $startnum)),
                           'label' => xarML('Date'));
        $fields[4] = array('url' => xarModURL('shopping',$level,'viewitems',
                                              array('search' => 4,
                                                    'sort' => $sort,
                                                    'sortorder' => $sortorder,
                                                    'catid' => $catid,
                                                    'startnum' => $startnum)),
                           'label' => xarML('Price'));
        $fields[5] = array('url' => xarModURL('shopping', $level,'viewitems',
                                              array('search' => 5,
                                                    'sort' => $sort,
                                                    'sortorder' => $sortorder,
                                                    'catid' => $catid,
                                                    'startnum' => $startnum)),
                           'label' => xarML('Status'));
        $fields[6] = array('url' => xarModURL('shopping', $level,'viewitems',
                                              array('search' => 6,
                                                    'sort' => $sort,
                                                    'sortorder' => $sortorder,
                                                    'catid' => $catid,
                                                    'startnum' => $startnum)),
                           'label' => xarML('Stock'));
        $fields[7] = array('url' => xarModURL('shopping', $level,'viewitems',
                                              array('search' => 7,
                                                    'sort' => $sort,
                                                    'sortorder' => $sortorder,
                                                    'catid' => $catid,
                                                    'startnum' => $startnum)),
                           'label' => xarML('Buys'));
        break;

      case 'recos':
        $fields[0] = array('url' => xarModURL('shopping', $level,'viewrecos',
                                              array('sort' => $sort,
                                                    'sortorder' => $sortorder,
                                                    'catid' => $catid,
                                                    'startnum' => $startnum)),
                           'label' => xarML('All'));
        $fields[1] = array('url' => xarModURL('shopping', $level,'viewrecos',
                                              array('search' => 1,
                                                    'sort' => $sort,
                                                    'sortorder' => $sortorder,
                                                    'catid' => $catid,
                                                    'startnum' => $startnum)),
                           'label' => xarML('Reco ID'));
        $fields[2] = array('url' => xarModURL('shopping',$level,'viewrecos',
                                              array('search' => 2,
                                                    'sort' => $sort,
                                                    'sortorder' => $sortorder,
                                                    'catid' => $catid,
                                                    'startnum' => $startnum)),
                           'label' => xarML('Item ID'));
        $fields[3] = array('url' => xarModURL('shopping',$level,'viewrecos',
                                              array('search' => 3,
                                                    'sort' => $sort,
                                                    'sortorder' => $sortorder,
                                                    'catid' => $catid,
                                                    'startnum' => $startnum)),
                           'label' => xarML('Item Name'));
        $fields[4] = array('url' => xarModURL('shopping',$level,'viewrecos',
                                              array('search' => 4,
                                                    'sort' => $sort,
                                                    'sortorder' => $sortorder,
                                                    'catid' => $catid,
                                                    'startnum' => $startnum)),
                           'label' => xarML('Submitted By'));
        break;
    }

    return $fields;
}
?>
