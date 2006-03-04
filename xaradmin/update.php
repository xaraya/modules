<?php
/*
 * Censor Module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage  Censor Module
 * @author John Cox
*/

/**
 * This function is called with the results of the
 * form supplied by censor_admin_modify() to update a current censored word
 *
 * @param  $ 'cid' the id of the censored word to be updated
 * @param  $ 'keyword' the censored word to be created
 * @param  $ 'case' censored word is case sensitive or not
 * @param  $ 'matchcase' how find censored word
 * @param  $ 'locale' the locale where the woord is censored
 */
function censor_admin_update($args)
{
    // Get parameters
    if (!xarVarFetch('cid', 'int:1:', $cid)) return;
    if (!xarVarFetch('keyword', 'str:1:', $keyword, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('case', 'isset', $case, 0,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('matchcase', 'isset', $matchcase, 0,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('locale', 'array', $locale, '',XARVAR_NOT_REQUIRED)) return;

    extract($args);

    if (empty($locale) || in_array('ALL', $locale)){
        $loc[] = 'ALL';
    } else {
        $loc = $locale;
    }

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;
    // Security Check

    if (!xarSecurityCheck('EditCensor')) return;
    if (!xarModAPIFunc('censor','admin','update',array('cid' => $cid,
                             'keyword' => $keyword,
                             'case' => $case,
                             'matchcase' => $matchcase,
                             'locale' => $loc))) return;

    xarResponseRedirect(xarModURL('censor', 'admin', 'view'));
    // Return
    return true;
}
?>
