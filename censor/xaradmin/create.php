<?php
/*
 * Censor Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage  Censor Module
 * @author John Cox
*/

/**
 * This function is called with the results of the
 * form supplied by censor_admin_new() to create a new censored word
 * 
 * @param  $ 'keyword' the censored word to be created
 * @param  $ 'case' censored word is case sensitive or not
 * @param  $ 'matchcase' how find censored word
 * @param  $ 'locale' the locale where the woord is censored
 */
function censor_admin_create($args)
{ 
    // Get parameters
    if (!xarVarFetch('keyword', 'str:1:', $keyword)) return;
    if (!xarVarFetch('case', 'isset', $case, 0,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('matchcase', 'isset', $matchcase, 0,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('locale', 'array', $locale, '',XARVAR_NOT_REQUIRED)) return;

extract($args); 

    if (empty($locale)) {
        $locale[] = xarConfigGetVar('Site.MLS.DefaultLocale');
    } 

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
                               'locale' => serialize($locale)));

    xarResponseRedirect(xarModURL('censor', 'admin', 'view')); 
    // Return
    return true;
} 
?>