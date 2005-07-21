<?php
/*/
 * displayitemdetails function
 * displays the details of an item
 *
 * @returns template variables
/*/
function shopping_user_displayitemdetails($args)
{
    // security check
    if (!xarSecurityCheck('ReadShoppingItems')) return;

    // get the args
    extract($args);

    if (!isset($iid)) return;
    if (!isset($showbox)) $showbox = true;
    if (!isset($desc)) {
      $item = xarModAPIFunc('shopping','user','getallitems',
                            array('where' => array('xar_iid' => array('=' => $iid))));
      $desc = $item[0]['description'];
    }
    if (!isset($summary)) {
      $item = xarModAPIFunc('shopping','user','getallitems',
                            array('where' => array('xar_iid' => array('=' => $iid))));
      $summary = $item[0]['summary'];
    }

    // init data array
    $data = array();
    $data['showbox'] = $showbox;
    $data['desc'] = $desc;
    $data['summary'] = $summary;

    // if user has additem permission, display a link to do so
    if (xarSecurityCheck('AddShoppingItems', 0)) {
      $data['addpicurl'] = xarModURL('shopping', 'user', 'displayitem',
                                     array('iid' => $iid,
                                           'phase' => 3,
                                           'picphase' => 2));
    }
    // if user has submitreco permission, display a link to do so
    if (xarSecurityCheck('DeleteShoppingRecos', 0) || (xarSecurityCheck('SubmitShoppingRecos', 0) && xarModGetVar('shopping', 'userecommendations'))) {
      $data['addrecourl'] = xarModURL('shopping', 'user', 'displayitem',
                                      array('iid' => $iid,
                                            'phase' => 4,
                                            'recophase' => 2));
    }
    if (xarSecurityCheck('EditShoppingItems', 0)) {
      $data['editurl'] = xarModURL('shopping', 'admin', 'edititem', array('iid' => $iid));
    }


    return $data;
}
?>