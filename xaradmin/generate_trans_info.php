<?php
/**
 * Translations Module
 *
 * @package modules
 * @subpackage translations module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/77.html
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

function translations_admin_generate_trans_info()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) {
        return;
    }

    if (!xarVarFetch('dnType', 'int', $dnType)) {
        return;
    }
    if (!xarVarFetch('dnName', 'str:1:', $dnName)) {
        return;
    }
    if (!xarVarFetch('extid', 'int', $extid)) {
        return;
    }

    $tplData['locales'] = xarConfigVars::get(null, 'Site.MLS.AllowedLocales');
    $tplData['release_locale'] = translations_release_locale();
    $tplData['archiver_path'] = xarMod::apiFunc('translations', 'admin', 'archiver_path');

    $druidbar = translations_create_druidbar(GENTRANS, $dnType, $dnName, $extid);
    $opbar = translations_create_opbar(GEN_TRANS, $dnType, $dnName, $extid);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    $tplData['dnType'] = $dnType;
    $tplData['dnTypeText'] = xarMLSContext::getContextTypeText($dnType);
    $tplData['dnName'] = $dnName;
    $tplData['extid'] = $extid;

    return $tplData;
}
