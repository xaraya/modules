<?php

/**
 * File: $Id$
 *
 * Generate module translations
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage modules
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

    if (!($modinfo = xarModGetInfo($modid))) return;
    $modname = $modinfo['name'];
    $moddir = $modinfo['osdirectory'];

    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $time = explode(' ', microtime());
    $startTime = $time[1] + $time[0];

    $l = xarLocaleGetInfo($locale);
//    if ($l['charset'] == 'utf-8') {
//        $ref_locale = $locale;
//    } else {
//        $l['charset'] = 'utf-8';
//        $ref_locale = xarLocaleGetString($l);
//    }
        $ref_locale = $locale;

    $backend = xarModAPIFunc('translations','admin','create_backend_instance',array('interface' => 'ReferencesBackend', 'locale' => $ref_locale));
    if (!isset($backend)) return;
    if (!$backend->bindDomain(XARMLS_DNTYPE_MODULE, $modname)) {
        $msg = xarML('Before generating translations you must first generate skels.');
        $link = array(xarML('Click here to proceed.'), xarModURL('translations', 'admin', 'update_info', array('ctxtype' => 'module')));
        xarExceptionSet(XAR_USER_EXCEPTION, 'MissingSkels', new DefaultUserException($msg, $link));
        return;
    }

    $allcontexts = $GLOBALS['MLS']->getContexts();
    foreach ($allcontexts as $context)
        $allCtxNames[$context->getType()] = $backend->getContextNames($context->getType());

    $gen = xarModAPIFunc('translations','admin','create_generator_instance',array('interface' => 'TranslationsGenerator', 'locale' => $locale));
    if (!isset($gen)) return;
    if (!$gen->bindDomain(XARMLS_DNTYPE_MODULE, $modname)) return;

    foreach ($allCtxNames as $ctxType => $ctxNames) {
        foreach ($ctxNames as $ctxName) {
            if (!$backend->loadContext($ctxType, $ctxName)) return;

            if (!$gen->create($ctxType, $ctxName)) return;

            $context = $GLOBALS['MLS']->getContextByType($ctxType);
            if ($context->getName() != '') $sName = $context->getName() . "::" . $ctxName;
            else $sName = $ctxName;

            $statistics[$sName] = array('entries'=>0, 'keyEntries'=>0);

            while (list($string, $translation) = $backend->enumTranslations()) {
                $statistics[$sName]['entries']++;
                $gen->addEntry($string, $translation);
            }

            while (list($key, $translation) = $backend->enumKeyTranslations()) {
                $statistics[$sName]['keyEntries']++;
                $gen->addKeyEntry($key, $translation);
            }

            $gen->close();
            $backend->clear();
        }
    }

    $time = explode(' ', microtime());
    $endTime = $time[1] + $time[0];

    return array('time' => $endTime - $startTime, 'statistics' => $statistics);
}
?>