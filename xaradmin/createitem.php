<?php
/*/
 * createitem
 * creates a new item from the info recieved from additem
 *
 * @redirects you to viewitems, addpic, addreco, or additem
/*/
function shopping_adminapi_createitem()
{
    // security check
    if (!xarSecurityCheck('AddShoppingItems')) return;
    // confirm auth key
    if (!xarSecConfirmAuthKey()) return;

    // get values from post
    list($iname,
         $iprice,
         $isummary,
         $idescription,
         $istock,
         $gonext,
         $cids) = xarVarCleanFromInput('iname',
                                       'iprice',
                                       'isummary',
                                       'idescription',
                                       'istock',
                                       'gonext',
                                       'cids');

    // set error var defaults
    $errflag = false;
    $errtext = "";
    $errwho = array();

    // check values from post and clean up more
    // check name
    if(empty($iname)) {
      // set the error flag and add text for this error
      $errflag = true;
      $errtext .= "&bull;&nbsp;" . xarML("You must enter a name for the product.") . "<br />";
      $errwho[] = 'iname';
    }

    // check price
    if(empty($iprice)) {
      // set the error flag and add text for this error
      $errflag = true;
      $errtext .= "&bull;&nbsp;" . xarML("You must enter a price for the product.") . "<br />";
      $errwho[] = 'iprice';
    } else {
      // remove any dollar signs from the left side
      $iprice = eregi_replace('\$', '', $iprice);
      $iprice = eregi_replace(',', '', $iprice);

      // get the price with no decimals into a new varialbe
      $pwd = eregi_replace('\.', '', $iprice);

      // check the format of the price withou a decimal
      if (eregi('[[:alpha:]]+|[[:punct:]]+|[[:cntrl:]]+', $pwd)) {
        $errflag = true;
        $errtext .= "&bull;&nbsp;" . xarML("You must enter a valid price for the product. <i>(e.g. 5.07, \$13.23, \$67)</i>") . "<br />";
        $errwho[] = 'iprice';
      } else {
        // round price and format it
        $iprice = round($iprice, 2);
        $iprice = number_format($iprice, 2, '.', '');
      }
    }

    // check description
    if (empty($idescription)) {
      $errflag = true;
      $errtext .= "&bull;&nbsp;" . xarML("You must enter a description for the product.") . "<br />";
      $errwho[] = 'idescription';
    }

    // check stock... default to zero
    if (empty($istock)) {
      $istock = 0;
    } else if ($istock < 0) {
      $istock = 0;
    }


    // check categorys... if none selected, default to no category.
    // I had thought about requiring a category as long as the module was hooked
    // but decided against it, due to the fact that the hook is optional in itself
    if (empty($cids) || !is_array($cids) ||
        // catch common mistake of using array('') instead of array()
        (count($cids) > 0 && empty($cids[0])) ) {
        $cids = array();
    }

    // if there was an error, you get sent back to the additem page with
    // the messages displayed, and the values you previously entered
    // already in the fields
    if ($errflag) {
      //$idescription = eregi_replace('\r', '%0D%0A', $idescription);
      $redirecturl = xarModURL('shopping', 'admin', 'additem',
                               array('iname' => $iname,
                                     'iprice' => $iprice,
                                     'isummary' => $isummary,
                                     'idescription' => $idescription,
                                     'istock' => $istock,
                                     'gonext' => $gonext,
                                     'errflag' => true,
                                     'errtext' => $errtext,
                                     'errwho' => $errwho));
      xarResponseRedirect($redirecturl);
    } else {
      // gets vars in proper type
      $iprice = (double) $iprice;
      $idate = date('Y-m-d');
      // status is dependant on stock
      $istock = (int) $istock;
      if ($istock == 0) {
        $istatus = 2;  // backordered
      } else if ($istock <= xarModGetVar('shopping', 'lowstock')) {
        $istatus = 1;  // low stock
      } else {
        $istatus = 0;
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
                xar_iid, xar_iname, xar_iprice,
                xar_isummary, xar_idescription, xar_istatus,
                xar_istock, xar_idate, xar_ibuys)
              VALUES (?,?,?,?,?,?,?,?,?)";
      $bindvars = array($iid, $iname, $iprice, $isummary, $idescription, $istatus, $istock, $idate,0);
      $result = &$dbconn->Execute($sql,$bindvars);
      if (!$result) return;
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

      // redirect to the proper page based on users choices
      switch ($gonext) {
        case 0:
          return xarModFunc('shopping','user','displayitem', array('iid' => $iid, 'phase' => 3, 'picphase' => 2));
          break;
        case 1:
          return xarModFunc('shopping','user','displayitem', array('iid' => $iid, 'phase' => 4, 'recophase' => 2));
          break;
        case 2:
          return xarModFunc('shopping','user','displayitem',array('iid' => $iid));
          break;
        case 3:
          return xarModFunc('shopping','admin','additem',array('success' => $iname));
          break;
        case 4:
        default:
          return xarModFunc('shopping','admin','viewitems');
          break;
      }
    }
}
?>