<?php
/**
 * This is a standard function that is called with the results of the
 * form supplied by censor_admin_modify() to update a current item
 * 
 * @param  $ 'cid' the id of the link to be updated
 * @param  $ 'keyword' the keyword of the link to be updated
 * @param  $ 'title' the title of the link to be updated
 * @param  $ 'url' the url of the link to be updated
 * @param  $ 'comment' the comment of the link to be updated
 */
function censor_admin_update($args)
{ 
    // Get parameters
    if (!xarVarFetch('cid', 'int:1:', $cid)) return;
    if (!xarVarFetch('obid', 'str:1:', $obid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('keyword', 'str:1:', $keyword, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('case', 'isset', $case, 0,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('matchcase', 'isset', $matchcase, 0,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('locale', 'str:1:', $locale, 'ALL',XARVAR_NOT_REQUIRED)) return;
    
    extract($args);
    
    if (!empty($obid)) {
        $cid = $onid;
    } 
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return; 
    // Security Check
    if (!xarSecurityCheck('EditCensor')) return;
    if (!xarModAPIFunc('censor',
                       'admin',
                       'update',
                       array('cid' => $cid,
                             'keyword' => $keyword,
                             'case' => $case,
                             'matchcase' => $matchcase,
                             'locale' => $locale))) return;
                
    xarResponseRedirect(xarModURL('censor', 'admin', 'view')); 
    // Return
    return true;
}
?>