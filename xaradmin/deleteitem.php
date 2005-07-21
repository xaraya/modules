<?php

/*/
 * deleteitem function
 * deletes an item from the database using the api function removeitem
 *
 * @redirects to view items
/*/
function shopping_admin_deleteitem()
{
    // security check
    if (!xarSecurityCheck('DeleteShoppingItems')) return;

    // get vars from url
    if(!xarVarFetch('startnum',  'isset', $startnum,  1,     XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('catid',     'isset', $catid,     NULL,  XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('sort',      'isset', $sort,      1,     XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('sortorder', 'isset', $sortorder, 'ASC', XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('search',    'isset', $search,    0,     XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('option1',   'isset', $option1,   NULL,  XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('option2',   'isset', $option2,   NULL,  XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('iid',       'isset', $iid,       NULL,  XARVAR_DONT_SET)) return;

    if (!xarModAPIFunc('shopping', 'admin', 'removeitem', array('iid' => $iid))) return;
    if (!xarModAPIFunc('shopping', 'admin', 'removepic', array('iid' => $iid))) return;
    if (!xarModAPIFunc('shopping', 'admin', 'removereco', array('iid' => $iid))) return;

    // TODO :: make it delete recos and pic for the item as well

    return xarModFunc('shopping','admin', 'viewitems',
                                  array('startnum' => $startnum,
                                        'catid' => $catid,
                                        'sort' => $sort,
                                        'sortorder' => $sortorder,
                                        'search' => $search,
                                        'option1' => $option1,
                                        'option2' => $option2));
}
?>
