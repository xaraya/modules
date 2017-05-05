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

function translations_admin_generate_trans_result()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('dnType','int',$dnType)) return;
    if (!xarVarFetch('dnName','str:1:',$dnName)) return;
    if (!xarVarFetch('extid','int',$extid)) return;

    $locale = translations_release_locale();
    $args = array('locale'=>$locale);
    switch ($dnType) {
        case xarMLS::DNTYPE_CORE:
        $res = xarMod::apiFunc('translations','admin','generate_core_trans',$args);
        break;
        case xarMLS::DNTYPE_MODULE:
        $args['modid'] = $extid;
        $res = xarMod::apiFunc('translations','admin','generate_module_trans',$args);
        break;
        case xarMLS::DNTYPE_THEME:
        $args['themeid'] = $extid;
        $res = xarMod::apiFunc('translations','admin','generate_theme_trans',$args);
        case xarMLS::DNTYPE_PROPERTY:
        $args['propertyid'] = $extid;
        $res = xarMod::apiFunc('translations','admin','generate_property_trans',$args);
        case xarMLS::DNTYPE_BLOCK:
        $args['blockid'] = $extid;
        $res = xarMod::apiFunc('translations','admin','generate_block_trans',$args);
        break;
    }

    if (!isset($res)) return;
    $data = $res;
    if ($data == NULL) {
        xarController::redirect(xarModURL('translations', 'admin', 'generate_trans_info'));
        return true;
    }

    $druidbar = translations_create_druidbar(GENTRANS, $dnType, $dnName, $extid);
    $opbar = translations_create_opbar(GEN_TRANS, $dnType, $dnName, $extid);
    $data = array_merge($data, $druidbar, $opbar);

    $data['dnType'] = $dnType;

    if ($dnType == xarMLS::DNTYPE_CORE)         $dnTypeText = 'core';
    elseif ($dnType == xarMLS::DNTYPE_THEME)    $dnTypeText = 'theme';
    elseif ($dnType == xarMLS::DNTYPE_MODULE)   $dnTypeText = 'module';
    elseif ($dnType == xarMLS::DNTYPE_PROPERTY) $dnTypeText = 'property';
    elseif ($dnType == xarMLS::DNTYPE_BLOCK)    $dnTypeText = 'block';
    else $dnTypeText = '';
    $data['dnTypeText'] = $dnTypeText;

    $data['dnName'] = $dnName;
    $data['extid'] = $extid;

    return $data;
}

?>