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
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Parse the generation request and show a result page.
 */
function translations_admin_generate_skels_result()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('dnType','int',$dnType)) return;
    if (!xarVarFetch('dnName','str:1:',$dnName)) return;
    if (!xarVarFetch('extid','int',$extid)) return;
    if (!xarVarFetch('dnTypeAll','bool',$dnTypeAll, false, XARVAR_NOT_REQUIRED)) return;
    $locale = translations_working_locale();
    $args = array('locale'=>$locale);
    switch ($dnType) {
        case xarMLS::DNTYPE_CORE:
        $res = xarMod::apiFunc('translations','admin','generate_core_skels',$args);
        break;
        case xarMLS::DNTYPE_MODULE:

            if ($dnTypeAll) {

                // Get all modules
                $installed = xarMod::apiFunc('modules', 'admin', 'getlist', array('filter' => array('State' => XARMOD_STATE_INSTALLED)));
                if (!isset($installed)) return;
                $uninstalled = xarMod::apiFunc('modules', 'admin', 'getlist', array('filter' => array('State' => XARMOD_STATE_UNINITIALISED)));
                if (!isset($uninstalled)) return;
                // Add modules to the list
                $modlist = array();
                foreach($uninstalled as $term) {
                    $modlist[] = $term['regid'];
                }
                foreach($installed as $term) {
                    $modlist[] = $term['regid'];
                }
                // Loop over the modlist and for each module generate the skels
                foreach($modlist as $extid) {
                    $args['modid'] = $extid;
                    $res = xarMod::apiFunc('translations','admin','generate_module_skels',$args);
                }

            } else {
                $args['modid'] = $extid;
                $res = xarMod::apiFunc('translations','admin','generate_module_skels',$args);
            }
        break;
        case xarMLS::DNTYPE_PROPERTY:
        $args['propertyid'] = $extid;
        $res = xarMod::apiFunc('translations','admin','generate_property_skels',$args);
        break;
        case xarMLS::DNTYPE_BLOCK:
        $args['blockid'] = $extid;
        $res = xarMod::apiFunc('translations','admin','generate_block_skels',$args);
        break;
        case xarMLS::DNTYPE_THEME:
        $args['themeid'] = $extid;
        $res = xarMod::apiFunc('translations','admin','generate_theme_skels',$args);
        break;
    }
    if (!isset($res)) return;

    $data = $res;
    if ($data == NULL) {
        xarController::redirect(xarModURL('translations', 'admin', 'generate_skels_info'));
    }

    $druidbar = translations_create_druidbar(GENSKELS, $dnType, $dnName, $extid);
    $opbar = translations_create_opbar(GEN_SKELS, $dnType, $dnName, $extid);
    $data['dnType'] = $dnType;

    if ($dnType == xarMLS::DNTYPE_CORE) $dnTypeText = 'core';
    elseif ($dnType == xarMLS::DNTYPE_THEME) $dnTypeText = 'theme';
    elseif ($dnType == xarMLS::DNTYPE_MODULE) $dnTypeText = 'module';
    elseif ($dnType == xarMLS::DNTYPE_PROPERTY) $dnTypeText = 'property';
    elseif ($dnType == xarMLS::DNTYPE_BLOCK) $dnTypeText = 'block';
    else $dnTypeText = '';
    $data['dnTypeText'] = $dnTypeText;
    $data['dnTypeAll']= $dnTypeAll;
    $data['dnName'] = $dnName;
    $data['extid'] = $extid;
    $data = array_merge($data, $druidbar, $opbar);

    return $data;
}

?>