<?php
/*/
 * viewpics function
 * displays the pics of an item based on id
 *
 * @returns template variables
/*/
function shopping_user_viewpics($args)
{
    // security check
    if (!xarSecurityCheck('ReadShoppingItems')) return;

    // get vars
    extract($args);

    if (!isset($iid)) return;
    if (!isset($showbox)) $showbox = true;
    // init data array
    $data = array();
    $data['showbox'] = $showbox;

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

    // get the pics
    $pics = xarModAPIFunc('shopping', 'user', 'getallpics', array('equals' => $iid));

    if (!$pics) {
      $data['nopics'] = true;
    } else {
      $data['pics'] = $pics;
    }

    return $data;
}
?>