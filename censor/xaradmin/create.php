<?php
/**
 * This is a standard function that is called with the results of the
 * form supplied by censor_admin_new() to create a new item
 * 
 * @param  $ 'keyword' the keyword of the link to be created
 * @param  $ 'title' the title of the link to be created
 * @param  $ 'url' the url of the link to be created
 * @param  $ 'comment' the comment of the link to be created
 */
function censor_admin_create($args)
{ 
    // Get parameters
    if (!xarVarFetch('keyword', 'str:1:', $keyword)) return;

    extract($args); 
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return; 
    // Security Check
    if (!xarSecurityCheck('EditCensor')) return; 
    // The API function is called
    $cid = xarModAPIFunc('censor',
        'admin',
        'create',
        array('keyword' => $keyword));

    xarResponseRedirect(xarModURL('censor', 'admin', 'view')); 
    // Return
    return true;
} 
?>