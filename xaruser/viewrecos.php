<?php
/*/
 * viewrecos function
 * displays the recos of an item based on id
 *
 * @returns template variables
/*/
function shopping_user_viewrecos($args)
{
    // security check
    if (!xarSecurityCheck('ViewShoppingRecos')) return;

    // get vars
    extract($args);

    if (!isset($iid)) return;
    if (!isset($showbox)) $showbox = true;
    if (!isset($startnum)) $startnum = 1;
    if (!isset($numrecos)) $numrecos = xarModGetVar('shopping', 'userrecosperpage');
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


    // get the recos
    $recos = xarModAPIFunc('shopping', 'user', 'getallrecos',
                           array('startnum' => $startnum,
                                 'where' => array('xar_iid1' => array('=' => $iid),
                                                  'xar_iid2' => array('=' => $iid))));
    // get the pager
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('shopping', 'user', 'countrecos',
                                                  array('where' => array('xar_iid1' => array('=' => $iid),
                                                                         'xar_iid2' => array('=' => $iid)))),
                                    xarModURL('shopping', 'user', 'displayitem',
                                              array('startnum' => '%%',
                                                    'iid' => $iid,
                                                    'phase' => 4,
                                                    'recophase' => 1)),
                                    $numrecos);

    $data['recos'] = array();
    if (!is_array($recos)) {
      // there are no recos for this item
      $data['norecos'] = true;
    } else {
      $i = 0; // this counts the column
      $j = 0; // this counts the total recos displayed
      foreach($recos as $reco) {
        // if reco['iid1'] == this item, use iid2 info
        if ($reco['iid1'] == $iid) {
          $reco['thisitem'] = $reco['iid1'];
          $reco['iid'] = $reco['iid2'];
          $reco['name'] = $reco['name2'];
        } else { // opposite of above
          $reco['thisitem'] = $reco['iid2'];
          $reco['iid'] = $reco['iid1'];
          $reco['name'] = $reco['name1'];
        }

        // get the first pic for this item to display
        $pics = xarModAPIFunc('shopping','user','getallpics',
                              array('equals' => $reco['iid']));

        if (!$pics) {
          // no pics exist for the recommended item
          $reco['firstpic'] = false;
        } else {
          // first pic for the item
          $reco['firstpic'] = $pics[0]['pic'];
        }
        $data['recos'][] = $reco;
      }

    }

    return $data;
}
?>