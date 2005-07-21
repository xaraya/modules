<?php

/*/
 * getallcart function
 * gets all items in the shopping cart for a user
 *
 * @returns $thing -- array containg all the retrieved info
 *
 * @param $uid -- the id of the user querying the database
 * @param $status -- 0 = cart; 1 = wishlist
/*/
function shopping_userapi_getallcart($args)
{
    // extract args and check them
    extract($args);
    if (!isset($uid)) $uid = xarUserGetVar('uid');
    if (isset($status)) {
      if ($status == 'cart') {
        $status = 0;
      } elseif ($status == 'wishlist') {
        $status = 1;
      } else {
        unset($status);
      }
    }

    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $carttable = $xartable['shopping_cart'];
    $itemstable = $xartable['shopping_items'];

    // produce sql string
    $bindvars = array();
    $sql = "SELECT
              $carttable.xar_iid, $carttable.xar_iquantity, $carttable.xar_cstatus,
              $itemstable.xar_iname, $itemstable.xar_iprice
            FROM $carttable, $itemstable
            WHERE $carttable.xar_uid = ?
              AND $carttable.xar_iid = $itemstable.xar_iid";
    $bindvars[] = $uid;

    // if $status is set add the where clause
    if (isset($status)) {
        $sql .= " AND $carttable.xar_cstatus = ?";
        $bindvars[] = $status;
    }
    // run query
    $result = &$dbconn->Execute($sql,$bindvars);
    if (!$result) return;

    // if no match was found return false
    if ($result->EOF) {
      return false;
    }

    // get results
    for (; !$result->EOF; $result->MoveNext()) {
      list($iid, $quantity, $kind, $name, $price) = $result->fields;
      // insert values into the array
      $thing[] = array('iid' => $iid, 'quantity' => $quantity, 'kind' => $kind,
                       'name' => $name, 'price' => $price);
    }

    // return the array
    return $thing;
}
?>
