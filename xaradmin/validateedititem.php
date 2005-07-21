<?php
/*/
 * validateedititem
 * makes sure an item is good from the info recieved from edititem
 *
 * @redirects you to viewitems or displayitem
/*/
function shopping_admin_validateedititem()
{
    // security check
    if (!xarSecurityCheck('EditShoppingItems')) return;
    // confirm auth key
    if (!xarSecConfirmAuthKey()) return;

    // get values from post
    list($iid,
         $iname,
         $iprice,
         $isummary,
         $idescription,
         $istock,
         $istatus,
         $gonext,
         $cids) = xarVarCleanFromInput('iid',
                                       'iname',
                                       'iprice',
                                       'isummary',
                                       'idescription',
                                       'istock',
                                       'istatus',
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

      // check the format of the price without a decimal
      if (!is_numeric($pwd)) {
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

    // if the discontinued check box isn't selected
    // check stock... default to zero
    if (!$istatus) {
      if (empty($istock)) {
        $istock = 0;
      } else if ($istock < 0) {
        $istock = 0;
      }
    } else {
      // item is discontinued
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
      $redirecturl = xarModURL('shopping', 'admin', 'edititem',
                               array('iid' => $iid,
                                     'iname' => $iname,
                                     'iprice' => $iprice,
                                     'isummary' => $isummary,
                                     'idescription' => $idescription,
                                     'istock' => $istock,
                                     'istatus' => $istatus,
                                     'gonext' => $gonext,
                                     'errflag' => true,
                                     'errtext' => $errtext,
                                     'errwho' => $errwho));
      xarResponseRedirect($redirecturl);
    } else {
      // call updateitem api function
      if (!xarModAPIFunc('shopping', 'admin', 'updateitem',
                         array('iid' => $iid,
                               'iname' => $iname,
                               'iprice' => $iprice,
                               'isummary' => $isummary,
                               'idescription' => $idescription,
                               'istock' => $istock,
                               'istatus' => $istatus,
                               'gonext' => $gonext,
                               'cids' => $cids))) return false;

      // redirect to the proper page based on users choices
      switch ($gonext) {
        case 0:
          return xarModFunc('shopping','user','displayitem',array('iid' => $iid));
          break;
        case 1:
        default:
          return xarModFunc('shopping','admin','viewitems');
          break;
      }
    }
}
?>