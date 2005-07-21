<?php

/*/
 * deletereco function
 * deletes a reco from the database using the api function removereco
 *
 * @redirects to view recos
/*/
function shopping_admin_deletereco()
{
    // security check
    if (!xarSecurityCheck('DeleteShoppingRecos')) return;

    // get vars from url
    if(!xarVarFetch('startnum',  'isset', $startnum,  1,     XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('catid',     'isset', $catid,     NULL,  XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('sort',      'isset', $sort,      1,     XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('sortorder', 'isset', $sortorder, 'ASC', XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('search',    'isset', $search,    0,     XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('option1',   'isset', $option1,   NULL,  XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('option2',   'isset', $option2,   NULL,  XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('twoiid',    'isset', $twoiid,    NULL,  XARVAR_DONT_SET)) return;
    if(!xarVarFetch('rid',       'isset', $rid,       NULL,  XARVAR_DONT_SET)) return;
    if(!xarVarFetch('iid',       'isset', $iid,       NULL,  XARVAR_DONT_SET)) return;
    if(!xarVarFetch('iid1',      'isset', $iid1,      NULL,  XARVAR_DONT_SET)) return;
    if(!xarVarFetch('iid2',      'isset', $iid2,      NULL,  XARVAR_DONT_SET)) return;
    
    if (isset($rid)) {
        if (!xarModAPIFunc('shopping', 'admin', 'removereco', array('rid' => $rid))) return;
    } elseif (isset($iid)) {
        if (!xarModAPIFunc('shopping', 'admin', 'removereco', array('iid' => $iid))) return;
    } elseif (isset($twoiid)) {
        if (isset($iid1) && isset($iid2)) {
          if (!xarModAPIFunc('shopping', 'admin', 'removereco', array('twoiid' => true, 'iid1' => $iid1, 'iid2' => $iid2))) return;
        } else {
          return;
        }
    }

    return xarModFunc('shopping','admin', 'viewrecos',
                                  array('startnum' => $startnum,
                                        'catid' => $catid,
                                        'sort' => $sort,
                                        'sortorder' => $sortorder,
                                        'search' => $search,
                                        'option1' => $option1,
                                        'option2' => $option2));
}
?>
