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
 * add new censored word
 */
function censor_admin_new()
{
    if (!xarVarFetch('keyword', 'str:1:', $keyword, "", XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('case', 'isset', $case, 0,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('matchcase', 'isset', $matchcase, 0,XARVAR_NOT_REQUIRED)) return;

    // Security Check
    if (!xarSecurityCheck('AddCensor')) return;
    $allowedlocales = xarConfigGetVar('Site.MLS.AllowedLocales');
    foreach($allowedlocales as $locale) {
       $data['locales'][] = array('name' => $locale, 'value' => $locale);
    }
    $data['authid'] = xarSecGenAuthKey();
    $data['keyword'] = $keyword;
    $data['case'] = $case;
    $data['match_case'] = $matchcase;

    return $data;
}

?>
