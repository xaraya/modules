<?php

/*/
 * countrecos function
 * gets a total count of recos, used for pager
 *
 * @returns $count -- the count retrieved
 *
 * @param $where -- an associative array where the key is 'where' and the value is 'equals'
 * @param $catid -- id of the category to filter by
/*/
function shopping_userapi_countrecos($args)
{
    extract($args);

    $thismodid = xarModGetIDFromName('shopping');
    $i = 0;
    $j = 0;

    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // start the query
    $recostable = $xartable['shopping_recommendations'];
    $sql = "SELECT COUNT(*) FROM $recostable";
    $bindvars = array();

    // check to see if we are filtering by a category
    if (isset($catid)) {
        // for some reason, this is the only way to get the linkage table short
        // of using the actual name.... but if the name changes.... well :)
        $catdef = xarModAPIFunc('categories','user','leftjoin',
                                            array('cids' => array($catid), // must put catid into an array
                                                  'modid' => $thismodid));
        // perform the necessary operations to get items in only the one category
        $cattable = $catdef['table'];
        $sql .= ", $cattable WHERE $cattable.xar_cid = $catid
                   AND ($cattable.xar_iid = $recostable.xar_iid1 OR $cattable.xar_iid = $recostable.xar_iid2)
                   AND $cattable.xar_modid = ?";
        $bindvars[] = $thismodid;
        }


        // if the where was set and is in proper fashion
        if (isset($where) && is_array($where)) {
            // start the where clause
            if (!isset($catid)) {
              $sql .= " WHERE";
            } else {
              $sql .= " AND";
            }
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
                    if ($what != 'xar_iid1') {
                      $sql .= " AND";
                    } else {
                      $sql .= " OR";
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

        // execute sql
        $result = &$dbconn->Execute($sql,$bindvars);
        if (!$result) return;
        // if no match was found return false
        if ($result->EOF) {
          return false;
        }

          // get the result into a var
          list($count) = $result->fields;

        // close the result
        $result->Close();

    return $count;
}
?>
