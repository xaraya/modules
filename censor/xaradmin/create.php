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
    if (!xarVarFetch('case', 'isset', $case, 0,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('matchcase', 'isset', $matchcase, 0,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('locale', 'str:1:', $locale, 'ALL',XARVAR_NOT_REQUIRED)) return;

extract($args); 

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return; 
    // Security Check
    if (!xarSecurityCheck('EditCensor')) return; 
    // The API function is called
    $cid = xarModAPIFunc('censor',
                         'admin',
                         'create',
                         array('keyword' => $keyword,
                               'case' => $case,
                               'matchcase' => $matchcase,
                               'locale' => $locale));

    xarResponseRedirect(xarModURL('censor', 'admin', 'view')); 
    // Return
    return true;
} 
?>