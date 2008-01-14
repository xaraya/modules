<?php
/**
 * Generate skels result
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
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

    $locale = translations_working_locale();
    $args = array('locale'=>$locale);
    switch ($dnType) {
        case XARMLS_DNTYPE_CORE:
        $res = xarModAPIFunc('translations','admin','generate_core_skels',$args);
        break;
        case XARMLS_DNTYPE_MODULE:

            if (!xarVarFetch('dnTypeAll','bool',$dnTypeAll, false, XARVAR_NOT_REQUIRED)) return;
            if ($dnTypeAll) {

                // Get all modules
                $installed = xarModAPIFunc('modules', 'admin', 'getlist', array('filter' => array('State' => XARMOD_STATE_INSTALLED)));
                if (!isset($installed)) return;
                $uninstalled = xarModAPIFunc('modules', 'admin', 'getlist', array('filter' => array('State' => XARMOD_STATE_UNINITIALISED)));
                if (!isset($uninstalled)) return;

                $modlist = array();
                foreach($uninstalled as $term) {
                    $modlist[] = $term['regid'];
                }
                //$modlist2 = array();
                foreach($installed as $term) {
                    $modlist[] = $term['regid'];
                }
                //$modlist = array_merge($modlist1,$modlist2);
                //ksort($modlist);
                // Loop over the modlist and for each module generate the skels
                foreach($modlist as $extid) {
                    $args['modid'] = $extid;
                    $res = xarModAPIFunc('translations','admin','generate_module_skels',$args);
                }
            } else {
                $args['modid'] = $extid;
                $res = xarModAPIFunc('translations','admin','generate_module_skels',$args);
            }
        break;
        case XARMLS_DNTYPE_THEME:
        $args['themeid'] = $extid;
        $res = xarModAPIFunc('translations','admin','generate_theme_skels',$args);
        break;
    }
    if (!isset($res)) return;

    $tplData = $res;
    if ($tplData == NULL) {
        xarResponseRedirect(xarModURL('translations', 'admin', 'generate_skels_info'));
    }

    $druidbar = translations_create_druidbar(GENSKELS, $dnType, $dnName, $extid);
    $opbar = translations_create_opbar(GEN_SKELS, $dnType, $dnName, $extid);
    $tplData['dnType'] = $dnType;

    if ($dnType == XARMLS_DNTYPE_CORE) $dnTypeText = 'core';
    elseif ($dnType == XARMLS_DNTYPE_THEME) $dnTypeText = 'theme';
    elseif ($dnType == XARMLS_DNTYPE_MODULE) $dnTypeText = 'module';
    else $dnTypeText = '';
    $tplData['dnTypeText'] = $dnTypeText;

    $tplData['dnName'] = $dnName;
    $tplData['extid'] = $extid;
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

?>