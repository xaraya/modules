<?php

/*/
 * edititem function
 * displays a form that allows you to edit the details of an item
 *
 * @returns template variables
/*/
function shopping_admin_edititem()
{
    // security check
    if (!xarSecurityCheck('EditShoppingItems')) return;

    // if we are here because of an error, we will have to get all these vars from a get
    list($errflag,
         $errtext,
         $iname,
         $iprice,
         $isummary,
         $idescription,
         $istock,
         $istatus,
         $errwho,
         $success,
         $gonext,
         $iid) = xarVarCleanFromInput('errflag',
                                        'errtext',
                                        'iname',
                                        'iprice',
                                        'isummary',
                                        'idescription',
                                        'istock',
                                        'istatus',
                                        'errwho',
                                        'success',
                                        'gonext',
                                        'iid');

    if (!isset($iid)) return;

    // init data array and generate authorization key
    $data = array();
    $data['authid'] = xarSecGenAuthKey();
    $data['iid'] = $iid;
    // set rest and submit button labels
    $data['resetlabel'] = xarML('Reset');
    $data['submitlabel'] = xarML('Edit Item');

    // get the details of the item
    $item = xarModAPIFunc('shopping', 'user', 'getallitems',
                           array('where' => array('xar_iid' => array('=' => $iid))));
    // set this var to the name of this item, so if an error is caused by no name being present,
    // a name will still appear in the heading....
    $data['thisname'] = $item[0]['name'];

    // set the form vars to defaults
    $data['iname'] = $item[0]['name'];
    $data['inamecol'] = '#000000';
    $data['iprice'] = $item[0]['price'];
    $data['ipricecol'] = '#000000';
    $data['idescription'] = $item[0]['description'];
    $data['idescriptioncol'] = '#000000';
    $data['isummary'] = $item[0]['summary'];
    $data['istock'] = $item[0]['stock'];

    if ($item[0]['status'] == 'Discontinued') {
      $data['istatus'] = 'checked';
    } else {
      $data['istatus'] = '';
    }

    $data['godisplay'] = 'selected';
    $data['goview'] = '';

    // call new hooks
    $hooks = xarModCallHooks('item','modify', $iid,
                             array('module' => 'shopping',
                                   'itemid' => $iid));
    if (empty($hooks)) {
        $hooks = '';
    }
    $data['hooks'] = $hooks;

    // if we were sent back to this page becaseu of an error, we need to set the form to what it used to be
    if (!empty($errflag)) {
      $data['iname'] = $iname;
      $data['iprice'] = $iprice;
      $data['isummary'] = urldecode($isummary);
      $data['idescription'] = urldecode($idescription);
      $data['istock'] = $istock;

      if ($istatus) {
        $data['istatus'] = 'checked';
      }

      $data['godsiplay'] = '';
      switch ($gonext) {
        case 0:
          $data['godisplay'] = 'selected';
          break;
        case 1:
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
