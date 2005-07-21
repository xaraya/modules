<?php
/*/
 * makepic
 * calls api function createpic
 *
 * @redirects you to addpic
/*/
function shopping_admin_makepic()
{
    // security check
    if (!xarSecurityCheck('AddShoppingItems')) return;
    // confirm auth key
    if (!xarSecConfirmAuthKey()) return;

    // get values from post
    list($iid, $ipic) = xarVarCleanFromInput('iid', 'ipic');

    // check values from post
    if(empty($iid)) return;
    if(empty($ipic)) return;

    // call api function
    if (!xarModAPIFunc('shopping', 'admin', 'createpic',
                       array('iid' => $iid,
                             'ipic' => $ipic))) return;

    return xarModFunc('shopping','user','displayitem', array('iid' => $iid, 'phase' => 3, 'picphase' => 2));
}
?>