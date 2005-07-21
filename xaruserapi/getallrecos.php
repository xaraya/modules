<?php

/*/
 * getallrecos function
 * gets recos
 *
 * @returns $thing -- array containg all the retrieved info
 *
 * @param $where -- an associative array where the key is 'where' and the value is another array containng how somwthing should be compared and what to compare to
 * @param $order -- an associative array where the key is 'order by' and the value is 'ASC' or 'DESC'
 * @param $catid -- id of the category to filter by
 * @param $numitems -- the number of items to return (max)
 * @param $startnum -- the number to start on
 * @param $distinct -- will append DISTINCT to the SELECT and return only the username
/*/
function shopping_userapi_getallrecos($args)
{
    extract($args);

    if (!isset($startnum)) {
      $startnum = 1;
    }
    if (!isset($numitems)) {
      $numitems = xarModGetVar('shopping', 'recosperpage');
    } elseif (!is_numeric($numitems) || $numitems < 1) {
      $numitems = xarModGetVar('shopping', 'recosperpage');
    }
    
    $thismodid = xarModGetIDFromName('shopping');
    $i = 0;
    $j = 0;

    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

        // start the query
        $recostable = $xartable['shopping_recommendations'];

        $select = "SELECT ";

        if (isset($distinct) && $distinct == true) {
          $select .= "DISTINCT $recostable.xar_uname";
        } else {
          $select .= "$recostable.xar_rid,
                      $recostable.xar_iid1,
                      $recostable.xar_iid2,
                      $recostable.xar_uname";
        }

        $from = " FROM $recostable";
        // cheack to see if we are filtering by a category and add the table to the from list
        if (isset($catid) && xarModIsHooked('categories', 'shopping')) {
          if (!xarModAPILoad('categories', 'user')) return;
          // for some reason, this is the only way to get the linkage table short
          // of using the actual name.... but if the name changes.... well :)
          $catdef = xarModAPIFunc('categories','user','leftjoin',
                                         array('cids' => array($catid), // must put catid into an array
                                               'modid' => $thismodid));
          $cattable = $catdef['table'];
          $from .= ", $cattable";
        }

        // start building actual query string
        $bindvars = array();
        $sql = "$select $from";

         // add where for cats
        if (isset($catid)) {
            $sql .= " WHERE $cattable.xar_cid = $catid
                   AND ($cattable.xar_iid = $recostable.xar_iid1 OR $cattable.xar_iid = $recostable.xar_iid2)
                   AND $cattable.xar_modid = ?";
            $bindvars[] = $thismodid;
        }

        // if the where arg was set and is in proper fashion
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
                  if ($what == 'xar_iid1') {
                    $sql .= " OR";
                  }
                } else {
                  $sql .= " $what $how " . $equals;
                  // if this is not the last entry, tag an AND to the end
                  if ($i < count($where)) {
                    if ($what == 'xar_iid1') {
                      $sql .= " OR";
                    } else {
                      $sql .= " AND";
                    }
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
              $sql .= " $recostable.$what $direction";
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

        // execute sql
        $result =& $dbconn->SelectLimit($sql, $numitems, $startnum-1,$bindvars);
        if (!$result) return false;
        // if no match was found return false
        if ($result->EOF) {
          return false;
        }

        for (; !$result->EOF; $result->MoveNext()) {
          // get the results into vars
          if (isset($distinct) && $distinct == true) {
            list ($uname) = $result->fields;
            $thing[] = array('uname' => $uname);
          } else {
            list($rid, $iid1, $iid2, $uname) = $result->fields;
            // get the names of the items from the items table
            $name1 = xarModAPIFunc('shopping', 'user', 'getallitems',
                                   array('where' => array ('xar_iid' => array('=' => $iid1))));
            $name2 = xarModAPIFunc('shopping', 'user', 'getallitems',
                                   array('where' => array ('xar_iid' => array('=' => $iid2))));

              $thing[] = array('rid' => $rid,
                               'iid1' => $iid1,
                               'name1' => $name1[0]['name'],
                               'iid2' => $iid2,
                               'name2' => $name2[0]['name'],
                               'uname' => $uname);
          }
        }

        // close the result
        $result->Close();

    return $thing;
}
?>
