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

function translations_admin_release_result()
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

    $locale = translations_release_locale();
    $args = array('locale'=>$locale);
    switch ($dnType) {
        case xarMLS::DNTYPE_CORE:
        $res = xarMod::apiFunc('translations', 'admin', 'release_core_trans', $args);
        break;
        case xarMLS::DNTYPE_MODULE:
        $args['modid'] = $extid;
        $res = xarMod::apiFunc('translations', 'admin', 'release_module_trans', $args);
        break;
        case xarMLS::DNTYPE_THEME:
        $args['themeid'] = $extid;
        $res = xarMod::apiFunc('translations', 'admin', 'release_theme_trans', $args);
        break;
    }
    if (!isset($res)) {
        return;
    }

    $filename = $res;
    if ($filename == null) {
        xarController::redirect(xarModURL('translations', 'admin', 'release_info'));
    }

    $tplData['url'] = sys::varpath().'/cache/'.$filename;

    $druidbar = translations_create_druidbar(REL, $dnType, $dnName, $extid);
    $opbar = translations_create_opbar(RELEASE, $dnType, $dnName, $extid);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}
