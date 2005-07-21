<?php
/*/
 * makereco
 * calls createreco api function
 *
 * @redirects you to displayreco (func=displayitem&iid=iid&phase=4)
/*/
function shopping_user_makereco()
{
    // security check
    if (!(xarSecurityCheck('DeleteShoppingRecos', 0) || (xarSecurityCheck('SubmitShoppingRecos') && xarModGetVar('shopping', 'userecommendations')))) return;

    // confirm auth key
    if (!xarSecConfirmAuthKey()) return;

    // get values from post
    list($iid1, $recos) = xarVarCleanFromInput('iid', 'recos');

    // check values from post
    if(empty($iid1)) return;
    if(empty($recos)) return;

    // call api function
    if (!xarModAPIFunc('shopping', 'user', 'createreco',
                       array('iid1' => $iid1,
                             'recos' => $recos))) return;

    return xarModFunc('shopping','user','displayitem', array('iid' => $iid1, 'phase' => 4));
}
?>