<?php

/*/
 * additem function
 * displays the form that gets the info for a new item
 *
 * @returns template variables
/*/
function shopping_admin_additem($args)
{
    // security check
    if (!xarSecurityCheck('AddShoppingItems')) return;

    // if we are here because of an error, we will have to get all these vars from a get
    if (!xarVarFetch('errflag', 'str:1:', $errflag, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('errtext', 'str:1:', $errtext, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('iname', 'str:1:', $iname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('iprice', 'str:1:', $iprice, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('isummary', 'str:1:', $isummary, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('idescription', 'str:1:', $idescription, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('istock', 'int:1', $istock, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('errwho', 'str:1:', $errwho, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('success', 'str:1:', $success, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('gonext', 'str:1:', $gonext, '', XARVAR_NOT_REQUIRED)) return;

    // get the args
    extract($args);

    // init data array and generate authorization key
    $data = array();
    $data['authid'] = xarSecGenAuthKey();
    // set rest and submit button labels
    $data['resetlabel'] = xarML('Reset');
    $data['submitlabel'] = xarML('Add Item');

    // set the form vars to defaults
    $data['iname'] = '';
    $data['inamecol'] = '#000000';
    $data['iprice'] = '';
    $data['ipricecol'] = '#000000';
    $data['idescription'] = '';
    $data['idescriptioncol'] = '#000000';
    $data['isummary'] = '';
    $data['istock'] = 0;

    $data['gopics'] = 'selected';
    $data['gorecos'] = '';
    $data['godisplay'] = '';
    $data['gonew'] = '';
    $data['goview'] = '';

    if (xarSecurityCheck('SubmitShoppingRecos', 0)) {
      $data['addrecos'] = true;
    } else {
      $data['addrecos'] = false;
    }

    // call new hooks
    $hooks = xarModCallHooks('item','new','shopping',
                             array('module' => 'shopping'));
    if (empty($hooks)) {
        $hooks = '';
    }
    $data['hooks'] = $hooks;

    // if we were sent here after adding an item we should let the user know that the item
    // was added successfully
    if (isset($success)) {
      $data['status'] = "\"$success\" added successfully!";
    }

    // if we were sent back to this page becaseu of an error, we need to set the form to what it used to be
    if (!empty($errflag)) {
      $data['iname'] = $iname;
      $data['iprice'] = $iprice;
      $data['isummary'] = urldecode($isummary);
      $data['idescription'] = urldecode($idescription);
      $data['istock'] = $istock;

      $data['gopics'] = '';
      switch ($gonext) {
        case 0:
          $data['gopics'] = 'selected';
          break;
        case 1:
          $data['gorecos'] = 'selected';
          break;
        case 2:
          $data['godisplay'] = 'selected';
          break;
        case 3:
          $data['gonew'] = 'selected';
          break;
        case 4:
          $data['goview'] = 'selected';
          break;
      }

      foreach ($errwho as $who) {
        if ($who == 'iname') {
          $data['inamecol'] = "#ff0000";
        } else if ($who == 'iprice') {
          $data['ipricecol'] = "#ff0000";
        } else if ($who == 'idescription') {
          $data['idescriptioncol'] = "#ff0000";
        }
      }
      $data['errinfo'] = urldecode($errtext);
    }

    // return template var
    return $data;
}
?>
