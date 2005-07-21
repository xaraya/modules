<?php

/*/
 * deletepic function
 * deletes a picture from an item using the api function removepic
 *
 * @redirects to view items
/*/
function shopping_admin_deletepic()
{
    // security check
    if (!xarSecurityCheck('DeleteShoppingItems')) return;

    // get vars from url
    if(!xarVarFetch('ipic', 'isset', $pic,  NULL,  XARVAR_DONT_SET)) return;
    if(!xarVarFetch('iid',  'isset', $iid,  NULL,  XARVAR_DONT_SET)) return;

    if (!xarModAPIFunc('shopping', 'admin', 'removepic', array('iid' => $iid,
                                                               'pic' => $pic))) {
      return;
    }

    return xarModFunc('shopping','user', 'displayitem',
                                  array('iid' => $iid,
                                        'phase' => 3));
}
?>
