<?php

/*/
 * getallpics function
 * gets pics for an item
 *
 * @returns $thing -- array containg all the retrieved info
 *
 * @param $where -- what field to seach on
 * @param $equals -- what $where equals
/*/
function shopping_userapi_getallpics($args)
{
    extract($args);

    if (!isset($where)) $where = "xar_iid";
    if (!isset($equals)) return;

    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $picstable = $xartable['shopping_items_pics'];

    // query
    $sql = "SELECT * FROM $picstable WHERE $where = ?";
    $result = &$dbconn->Execute($sql,array($equals));
    if (!$result) return;

    // return false if no pics were returned
    if ($result->EOF) {
      return false;
    }

    // loop through results
    for (; !$result->EOF; $result->MoveNext()) {
      list ($iid, $pic) = $result->fields;
      $thing[] = array('iid' => $iid,
                       'pic' => $pic);
    }

    return $thing;
}
?>
