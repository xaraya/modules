<?php
/*/
 * createitem
 * creates a new item from the info recieved from additem
 *
 * @return boolean
/*/
function shopping_adminapi_createitem($args)
{
    // security check
    if (!xarSecurityCheck('AddShoppingItems')) return;

    // get args
    extract($args);
    
    // gets vars in proper type
    $iprice = (double) $iprice;
    $idate = date('Y-m-d');
    // status is dependant on stock
    $istock = (int) $istock;
    $istatus = 0; // assume normal
    if ($istock == 0) {
        $istatus = 2;  // backordered
    } elseif ($istock <= xarModGetVar('shopping', 'lowstock')) {
        $istatus = 1;  // low stock
    }

    // get database setup and items table
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $itemstable = $xartable['shopping_items'];

    // get next available auto-increment values
    $iid = $dbconn->GenId($itemstable);
    // call transform hooks for the summary and description
    $isummary     = xarModCallHooks('item', 'transform-input', $iid, $isummary,     'shopping');
    $idescription = xarModCallHooks('item', 'transform-input', $iid, $idescription, 'shopping');

    // SQL to insert the item
    $sql = "INSERT INTO $itemstable (
                xar_iid, xar_iname, xar_iprice, xar_isummary,
                xar_idescription, xar_istatus, xar_istock,
                xar_idate, xar_ibuys)
              VALUES (?,?,?,?,?,?,?,?,?)";
    $bindvars = array($iid, $iname, $iprice, $isummary, $idescription, $istatus, $istock, $idate, 0);
    $result = &$dbconn->Execute($sql,$bindvars);
    if (!$result) return false;
    // get the iid we just entered into the DB
    $iid = $dbconn->PO_Insert_ID($itemstable, 'xar_iid');
    // close result set
    $result->Close();
    // call create hooks
    xarModCallHooks('item', 'create', $iid,
                        array('iid' => $iid,
                              'module' => 'shopping',
                              'itemid' => $iid,
                              'cids' => $cids),
                        'shopping');
    return $iid;
}
?>