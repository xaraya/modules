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
 * modify a censored word item
 *
 * @param  $ 'cid' the id of the censored word to be modified
 */
function censor_admin_modify($args)
{
    // Get parameters
    if (!xarVarFetch('cid', 'int:1:', $cid)) return;
    if (!xarVarFetch('obid', 'str:1:', $obid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('keyword', 'str:1:', $newkey, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('case', 'isset', $newcase, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('matchcase', 'isset', $newmatchcase, NULL, XARVAR_NOT_REQUIRED)) return;

    extract($args);
    $data = array();

    if (!empty($obid)) {
        $cid = $obid;
    }

    $data = xarModAPIFunc('censor', 'user','get', array('cid' => $cid));

    if ($data == false) return;


    // Security Check
    if (!xarSecurityCheck('EditCensor')) return;

    $data['locale'] = unserialize($data['locale']);
    $data['authid'] = xarSecGenAuthKey();


    if (isset($newkey)) {
        $data['keyword'] = $newkey;
        $data['case_sensitive'] = $newcase;
        $data['match_case'] = $newmatchcase;
        }

    $allowedlocales[] = "ALL";
    $sitelocales = xarConfigGetVar('Site.MLS.AllowedLocales');
    foreach($sitelocales as $locale) {
        $allowedlocales[] = $locale;
    }

     foreach($allowedlocales as $loc) {
    if (in_array($loc, $data['locale'])) {
        $data['locales'][] = array('name' => $loc, 'value' => $loc, 'check' => '1');
       } else {
        $data['locales'][] = array('name' => $loc, 'value' => $loc, 'check' => '0');
      }
      }

    return $data;

}
?>
