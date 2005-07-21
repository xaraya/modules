<?php

/*/
 * addpic function
 * displays the form that gets the info for a new pic
 *
 * @returns template variables
/*/
function shopping_admin_addpic($args)
{
    // security check
    if (!xarSecurityCheck('AddShoppingItems')) return;

    // get args
    extract($args);
    if (!isset($iid)) return;
    if (!isset($showbox)) $showbox = true;

    // init data array and generate authorization key
    $data = array();
    $data['authid'] = xarSecGenAuthKey();
    $data['showbox'] = $showbox;
    $data['iid'] = $iid;

    // set submit button label
    $data['submitlabel'] = xarML('Add Picture');

    // if user has submitreco permission, display a link to do so
    if (xarSecurityCheck('DeleteShoppingRecos', 0) || (xarSecurityCheck('SubmitShoppingRecos', 0) && xarModGetVar('shopping', 'userecommendations'))) {
      $data['addrecourl'] = xarModURL('shopping', 'user', 'displayitem',
                                      array('iid' => $iid,
                                            'phase' => 4,
                                            'recophase' => 2));
    }
    // if user has edit item permission allow them
    if (xarSecurityCheck('EditShoppingItems', 0)) {
      $data['editurl'] = xarModURL('shopping', 'admin', 'edititem',
                                     array('iid' => $iid));
    }


    return $data;
}
?>
