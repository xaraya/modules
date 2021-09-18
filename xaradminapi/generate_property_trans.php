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

/**
 * generate translations for the specified theme
 * @param $args['propertyid'] property registry identifier
 * @param $args['locale'] locale name
 * @returns array
 * @return statistics on generation process
 */
function translations_adminapi_generate_property_trans($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($propertyid) && isset($locale)');

    if (!($modinfo = xarMod::getInfo($themeid, 'theme'))) {
        return;
    }
    $themename = $modinfo['name'];
    $themedir = $modinfo['osdirectory'];

    // Security Check
    if (!xarSecurity::check('AdminTranslations')) {
        return;
    }

    $time = explode(' ', microtime());
    $startTime = $time[1] + $time[0];

    if (xarConfigVars::get(null, 'Site.MLS.TranslationsBackend') == 'xml2php') {
        $l = xarMLS::localeGetInfo($locale);
        if ($l['charset'] == 'utf-8') {
            $ref_locale = $locale;
        } else {
            $l['charset'] = 'utf-8';
            $ref_locale = xarMLS::localeGetString($l);
        }
    } else {
        $ref_locale = $locale;
    }

    $backend = xarMod::apiFunc('translations', 'admin', 'create_backend_instance', ['interface' => 'ReferencesBackend', 'locale' => $ref_locale]);
    if (!isset($backend)) {
        return;
    }
    if (!$backend->bindDomain(xarMLS::DNTYPE_THEME, $themename)) {
        $msg = xarML('Before generating translations you must first generate skels.');
        $link = [xarML('Click here to proceed.'), xarController::URL('translations', 'admin', 'update_info', ['dntype' => 'theme'])];
        throw new Exception($msg);
    }

    $gen = xarMod::apiFunc('translations', 'admin', 'create_generator_instance', ['interface' => 'TranslationsGenerator', 'locale' => $locale]);
    if (!isset($gen)) {
        return;
    }
    if (!$gen->bindDomain(xarMLS::DNTYPE_THEME, $themename)) {
        return;
    }

    $theme_contexts_list[] = 'themes:'.$themename.'::common';

    //$subnames = xarMod::apiFunc('translations','admin','get_theme_files',array('themedir'=>$themedir));
    //foreach ($subnames as $subname) {
    //    $theme_contexts_list[] = 'themes:'.$themename.'::'.$subname;
    //}

    $dirnames = xarMod::apiFunc('translations', 'admin', 'get_theme_dirs', ['themedir'=>$themedir]);
    $pattern = '/^([a-z0-9\-_]+)\.xt$/i';
    foreach ($dirnames as $dirname) {
        $subnames = xarMod::apiFunc(
            'translations',
            'admin',
            'get_theme_files',
            ['themedir'=>"themes/$themedir/$dirname",'pattern'=>$pattern]
        );
        foreach ($subnames as $subname) {
            $theme_contexts_list[] = 'themes:'.$themename.':'.$dirname.':'.$subname;
        }
    }

    foreach ($theme_contexts_list as $theme_context) {
        [$dntype1, $dnname1, $ctxtype1, $ctxname1] = explode(':', $theme_context);
        $ctxType = 'themes:'.$ctxtype1;
        $ctxName = $ctxname1;

        if (!$backend->loadContext($ctxType, $ctxName)) {
            return;
        }
        if (!$gen->create($ctxType, $ctxName)) {
            return;
        }

        if ($ctxtype1 != '') {
            $sName = $ctxtype1 . "::" . $ctxName;
        } else {
            $sName = $ctxName;
        }

        $statistics[$sName] = ['entries'=>0, 'keyEntries'=>0];
        while ([$string, $translation] = $backend->enumTranslations()) {
            $statistics[$sName]['entries']++;
            $gen->addEntry($string, $translation);
        }
        while ([$key, $translation] = $backend->enumKeyTranslations()) {
            $statistics[$sName]['keyEntries']++;
            $gen->addKeyEntry($key, $translation);
        }
        $gen->close();
        $backend->clear();
    }

    $time = explode(' ', microtime());
    $endTime = $time[1] + $time[0];

    return ['time' => $endTime - $startTime, 'statistics' => $statistics];
}
