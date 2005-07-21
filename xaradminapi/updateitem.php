<?php
/*/
 * updateitem
 * updates an item from the info recieved from edititem
 *
 * @redirects you to viewitems or displayitem
/*/
function shopping_adminapi_updateitem($args)
{
    // security check
    if (!xarSecurityCheck('EditShoppingItems')) return;

    // extract args
    extract($args);
      
      // gets vars in proper type
      $iprice = (double) $iprice;
      $idate = date('Y-m-d');
      // status is dependant on stock
      $istock = (int) $istock;
      if ($istock == 0) {
        if (!$istatus) {
          $level = 2;  // backordered
        } else {
          $level = 3;
        }
      } else if ($istock <= xarModGetVar('shopping', 'lowstock')) {
        $level = 1;  // low stock
      } else {
        $level = 0;
      }

      // get database setup and items table
      $dbconn =& xarDBGetConn();
      $xartable =& xarDBGetTables();
      $itemstable = $xartable['shopping_items'];

      // call transform hooks for the description
      $idescription = xarModCallHooks('item', 'transform-input', $iid, $idescription, 'shopping');

      // SQL to insert the item
      $sql = "UPDATE $itemstable
              SET xar_iname = ?,
                  xar_iprice = ?,
                  xar_isummary = ?,
                  xar_idescription = ?,
                  xar_istatus = ?,
                  xar_istock = ?
              WHERE xar_iid = ?";
      $bindvars = array($iname, $iprice, $isummary, $idescription, $level, $istock, $iid);

      $result = &$dbconn->Execute($sql,$bindvars);
      if (!$result) return false;
      // close result set
      $result->Close();

      // call update hooks
      xarModCallHooks('item', 'update', $iid,
                      array('iid' => $iid,
                            'module' => 'shopping',
                            'itemid' => $iid,
                            'cids' => $cids));

    return true;
}
?>