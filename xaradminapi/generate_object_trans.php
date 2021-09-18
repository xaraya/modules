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
function translations_adminapi_generate_object_trans($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($objectid) && isset($locale)');

    $object = xarMod::apiFunc('dynamicdata', 'user', 'getobjectlist', ['objectid' => $objectid]);
    if (!is_object($object)) {
        return;
    }
    $objectlabel = $object->label;
    $objectname = $object->name;

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
        $link = [xarML('Click here to proceed.'), xarController::URL('translations', 'admin', 'update_info', ['dntype' => 'object'])];
        throw new Exception($msg);
    }

    $gen = xarMod::apiFunc('translations', 'admin', 'create_generator_instance', ['interface' => 'TranslationsGenerator', 'locale' => $locale]);
    if (!isset($gen)) {
        return;
    }
    if (!$gen->bindDomain(xarMLS::DNTYPE_THEME, $themename)) {
        return;
    }

    $object_contexts_list[] = 'objects:'.$objectname.'::common';

    // Disable any properties that re not translatable
    foreach ($tplData['object']->properties as $name => $property) {
        // We need the ID as a reference: include it in any case
        if ($property->type == 21) {
            $tplData['object']->properties[$name]->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE);
            continue;
        }

        if (!$property->translatable) {
            $tplData['object']->properties[$name]->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISABLED);
        } else {
            // We need translatable properties to be shown in listings for this exercise
            $tplData['object']->properties[$name]->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE);
            $object_contexts_list[] = 'objects:'.':'.$objectname.':'.$name;
        }
    }

    foreach ($object_contexts_list as $object_context) {
        [$dntype1, $dnname1, $ctxtype1, $ctxname1] = explode(':', $object_context);
        $ctxType = 'objects:'.$ctxtype1;
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
