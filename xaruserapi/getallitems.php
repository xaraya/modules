<?php

/*/
 * getallitems function
 * gets items
 *
 * @returns $thing -- array containg all the retrieved info (date field is returned as a unix timestamp)
 *
 * @param $where -- an associative array where the key is 'where' and the value is another array containng how somwthing should be compared and what to compare to
 * @param $order -- an associative array where the key is 'order by' and the value is 'ASC' or 'DESC'
 * @param $catid -- id of the category to filter by
 * @param $getratings -- returns the ratings on items if set
 * @param $gethits -- returns the hits on items if set
 * @param $numitems -- the number of items to return (max)
 * @param $startnum -- the number to start on
 * @param $cids -- returns the cids of the last item gotten
/*/
function shopping_userapi_getallitems($args)
{
    extract($args);

    if (!isset($catid)) {
      $catid = NULL;
    } else {
      if (eregi('-', $catid)) {
        $catids = explode('-', $catid);
      } elseif (eregi('[[:space:]]', $catid)) {
        $catids = explode(' ', $catid);
      } else {
        $catids = array($catid);
      }
    }

    if (!isset($startnum)) {
      $startnum = 1;
    }
    if (!isset($numitems) || !is_numeric($numitems) || $numitems < 1) {
      $numitems = xarModGetVar('shopping', 'itemsperpage');
    }

    $thismod = xarModGetIDFromName('shopping');
    $i = 0;
    $j = 0;

    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

        // start the query
        $itemstable = $xartable['shopping_items'];
        $bindvars = array();
        $select = "SELECT $itemstable.xar_iid,
                       $itemstable.xar_iname,
                       $itemstable.xar_iprice,
                       $itemstable.xar_isummary,
                       $itemstable.xar_idescription,
                       $itemstable.xar_istatus,
                       $itemstable.xar_istock,
                       UNIX_TIMESTAMP($itemstable.xar_idate),
                       $itemstable.xar_ibuys";

        $from = " FROM $itemstable";
        // cheack to see if we are filtering by a category and add the table to the from list
        if (isset($catid) && xarModIsHooked('categories', 'shopping')) {
          if (!xarModAPILoad('categories', 'user')) return;
          // for some reason, this is the only way to get the linkage table short
          // of using the actual name.... but if the name changes.... well :)
          $catdef = xarModAPIFunc('categories','user','leftjoin',
                                         array('cids' => $catids, // must put catid into an array
                                               'modid' => $thismod));
          $cattable = $catdef['table'];
          $from .= ", $cattable";
        }

        // check to see if we are returning ratings
        if (isset($getratings) && xarModIsHooked('ratings','shopping')) {
          if (!xarModAPILoad('ratings', 'user')) return;
          $ratingsdef = xarModAPIFunc('ratings','user','leftjoin',
                                      array('modid' => $thismod,
                                            'itemtype' => null));
          $ratingstable = $ratingsdef['table'];
          $from .= " LEFT JOIN $ratingstable ON $ratingstable.xar_moduleid = $thismod AND $ratingstable.xar_itemid = $itemstable.xar_iid";
          $select .= ", $ratingstable.xar_rating";
        }

        // check to see if we are returning hits
        if (isset($gethits) && xarModIsHooked('hitcount','shopping')) {
          if (!xarModAPILoad('hitcount', 'user')) return;
          $hitsdef = xarModAPIFunc('hitcount','user','leftjoin',
                                      array('modid' => $thismod,
                                            'itemtype' => null));
          $hitstable = $hitsdef['table'];
          $from .= " LEFT JOIN $hitstable ON " . $hitsdef['field'] . " = $itemstable.xar_iid";
          $select .= ", $hitstable.xar_hits";
        }
        // start building actual query string
        $sql = "$select $from";

         // add where for cats
        if (isset($catid)) {
             $sql .= " WHERE $cattable.xar_cid IN ";
             foreach($catids as $q) {
               $catwhere[] = "$q";
             }
             $catw = join(', ', $catwhere);
             $sql .= '(' . $catw . ')';
             $sql .= " AND $cattable.xar_iid = $itemstable.xar_iid
                       AND $cattable.xar_modid = " . $thismod;
        }

        // if the where was set and is in proper fashion
        if (isset($where) && is_array($where)) {
            // start the where clause
            if (!isset($catid)) {
              $sql .= " WHERE";
            } else {
              $sql .= " AND";
            }

            // 'where' => array($wherewhat => array('=' => $equals))
            foreach ($where as $what => $compareto) {
              foreach ($compareto as $how => $equals) {
                $i++;
                // if $equals is an array then we are passing a where clause that is between 2 values
                if (is_array($equals)) {
                  $sql .= " $what BETWEEN ? AND ?";
                    $bindvars[] = $equals[0]; $bindvars[] = $equals[1];
                } else {
                  $sql .= " $what $how " . $equals;
                  // if this is not the last entry, tag an AND to the end
                  if ($i < count($where)) {
                    $sql .= " AND";
                  }
                }
              }
            }
        } elseif (isset($where) && !is_array($where)) { // $where is set, but its not an array
          $msg = xarML('Invalid #(1) for #(2) function #(3)() in #(4) module',
                       'where parameter', 'user', 'get',
                       'Shopping');
          xarErrorSet(XAR_USER_EXCEPTION, 'PARAM_NOT_ARRAY',
                         new SystemException($msg));
          return false;
        }

        // if the order was set and is in proper fashion
        if (isset($order) && is_array($order)) {
          // start the order by clause
          $sql .= " ORDER BY";
          foreach ($order as $what => $direction) {
            $j++;
            if ($what == 'xar_rating') {
              $sql .= " $ratingstable.$what $direction";
            } elseif ($what == 'xar_hits') {
              $sql .= " $hitstable.$what $direction";
            } else {
              $sql .= " $itemstable.$what $direction";
            }
            // if this is not the last entry, we need to tag a ',' to the end
            if ($j < count($order)) {
              $sql .= ", ";
            }
          }
        } else if (isset($order) && !is_array($order)) { // $order is set, but its not an array
          $msg = xarML('Invalid #(1) for #(2) function #(3)() in #(4) module',
                       'where parameter', 'user', 'get',
                       'Shopping');
          xarErrorSet(XAR_USER_EXCEPTION, 'PARAM_NOT_ARRAY',
                         new SystemException($msg));
          return false;
        }

     //   echo $sql; break;

        // execute sql
        $result =& $dbconn->SelectLimit($sql, $numitems, $startnum-1, $bindvars);
        if (!$result) return;
        // if no match was found return false
        if ($result->EOF) {
          return false;
        }

        for (; !$result->EOF; $result->MoveNext()) {
          // get the results into vars
          $allfields = $result->fields;

          // if ratings have been choosen and the field is empty
          if (isset($getratings) && xarModIsHooked('ratings', 'shopping')) {
            if (empty($allfields[9])) {
              $allfields[9] = "Not Rated";
            } else {
              $allfields[9] = intval($allfields[9]);
            }
          } elseif (isset($gethits) && xarModIsHooked('hitcount', 'shopping')) { // no ratings, but hitcount is selected
            $allfields[10] = $allfields[9]; // in this circumnstance, $allfield[7] will contan the hitcount but we assign hitcount 8 below
          } else {  // neither has been selected
            $allfields[9] = "";
            $allfields[10] = "";
          }

          // if only ratigs or hits are being used we have to put nothing in 8
          if (!isset($allfields[10])) {
            $allfields[10] = "";
          }

          // format price and status and date
          $allfields[2] = round($allfields[2], 2);
          $allfields[2] = '$' . number_format($allfields[2], 2, '.', '');

          if ($allfields[5] == 0) {
            $allfields[5] = "In stock";
          } elseif ($allfields[5] == 1) {
            $allfields[5] = "Low stock";
          } elseif ($allfields[5] == 2) {
            $allfields[5] = "Backordered";
          } elseif ($allfields[5] == 3) {
            $allfields[5] = "Discontinued";
          }

          $allfields[7] = date('F jS, Y', $allfields[7]);
          // will return the cids of the last item gotten.... usually only used when getting one item
          $returncids=array();
          if (!empty($cids)) {
              if (!xarModAPILoad('categories', 'user')) return;
              
              $shoppingcids = xarModAPIFunc('categories',
                                            'user',
                                            'getlinks',
                                            array('iids' => Array($allfields[0]),
                                                  'modid' => $thismod,
                                                  'reverse' => 0));
              if (is_array($shoppingcids) && count($shoppingcids) > 0) {
                  $returncids = array_keys($shoppingcids);
              }
          }
        $thing[] = array('iid' => $allfields[0],
                           'name' => $allfields[1],
                           'price' => $allfields[2],
                           'summary' => $allfields[3],
                           'description' => $allfields[4],
                           'status' => $allfields[5],
                           'stock' => $allfields[6],
                           'date' => $allfields[7],
                           'buys' => $allfields[8],
                           'rating' => $allfields[9],
                           'hits' => $allfields[10],
                           'catid' => $catid,
                           'cids' => $returncids);
        }



        // close the result
        $result->Close();
    
    return $thing;
}
?>
