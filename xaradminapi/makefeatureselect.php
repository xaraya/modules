<?php

/*/
 * makefeatureselect function
 * makes a selct form item to select the feature category
 *
 * @returns the html for the select
/*/
function shopping_adminapi_makefeatureselect()
{
    // load visual api for cats
    if (!xarModAPILoad('categories', 'visual')) return;

    // get master cids and explode into an array
    $mastercid = xarModGetVar('shopping', 'mastercids');
    $mastercid = explode(";", $mastercid);

    // loop through the cats and get all cats that are children
    foreach ($mastercid as $cid) {
      $allcats[] = xarModAPIFunc('categories','visual','treearray',
                                 array('cid' => $cid,
                                       'return_itself' => true));
    }

    // get feature cid
    $featurecid = xarModGetVar('shopping', 'featurecat');

    // check to see if the current featured cat is available
    $featureavail = false;
    foreach($allcats as $cats) {
      foreach($cats as $cat) {
        if ($cat['id'] == $featurecid) {
          $featureavail = true;
        }
      }
    }

    // if its not available, set it to the first available
    if (!$featureavail) {
      xarModSetVar('shopping', 'featurecat', $allcats[0][0]['id']);
      $featurecid = $allcats[0][0]['id'];
    }

    // build select
    // TODO: move this to a template
    $select = "<select name='featurecat' id='featurecat'>";
    foreach($allcats as $cats) {
      foreach($cats as $cat) {
        if ($cat['id'] == $featurecid) {
          $select .= "<option selected value=" . $cat['id'] . ">" . $cat['name'] . "</option>";
        } else {
          $select .= "<option value=" . $cat['id'] . ">" . $cat['name'] . "</option>";
        }
      }
    }
    $select .= "</select>";

    return $select;
}

?>