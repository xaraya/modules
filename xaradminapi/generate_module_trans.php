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
 * generate translations for the specified module
 * @param $args['modid'] module registry identifier
 * @param $args['locale'] locale name
 * @returns array
 * @return statistics on generation process
 */
function translations_adminapi_generate_module_trans($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($modid) && isset($locale)');

    if (!($modinfo = xarMod::getInfo($modid))) {
        return;
    }
    $modname = $modinfo['name'];
    $moddir = $modinfo['osdirectory'];

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
    if (!$backend->bindDomain(xarMLS::DNTYPE_MODULE, $modname)) {
        $msg = xarML('Before generating translations you must first generate skels.');
        $link = [xarML('Click here to proceed.'), xarController::URL('translations', 'admin', 'update_info', ['dntype' => 'module'])];
        throw new Exception($msg);
    }

    $gen = xarMod::apiFunc('translations', 'admin', 'create_generator_instance', ['interface' => 'TranslationsGenerator', 'locale' => $locale]);
    if (!isset($gen)) {
        return;
    }
    if (!$gen->bindDomain(xarMLS::DNTYPE_MODULE, $modname)) {
        return;
    }

    $module_contexts_list[] = 'modules:'.$modname.'::common';

    $subnames = xarMod::apiFunc('translations', 'admin', 'get_module_phpfiles', ['moddir'=>$moddir]);
    foreach ($subnames as $subname) {
        $module_contexts_list[] = 'modules:'.$modname.'::'.$subname;
    }

    $dirnames = xarMod::apiFunc('translations', 'admin', 'get_module_dirs', ['moddir'=>$moddir]);
    foreach ($dirnames as $dirname) {
        if (!preg_match('!^templates!i', $dirname, $matches)) {
            $pattern = '/^([a-z0-9\-_]+)\.php$/i';
        } else {
            $pattern = '/^([a-z0-9\-_]+)\.xt$/i';
        }
        $subnames = xarMod::apiFunc(
            'translations',
            'admin',
            'get_module_files',
            ['moddir'=>sys::code() . "modules/$moddir/xar$dirname",'pattern'=>$pattern]
        );
        foreach ($subnames as $subname) {
            $module_contexts_list[] = 'modules:'.$modname.':'.$dirname.':'.$subname;
        }
    }

    foreach ($module_contexts_list as $module_context) {
        [$dntype1, $dnname1, $ctxtype1, $ctxname1] = explode(':', $module_context);
        $ctxType = 'modules:'.$ctxtype1;
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
