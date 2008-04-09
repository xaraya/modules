<?php
/**
 * Generate core translation
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Caninin
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


function translations_adminapi_generate_core_trans($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($locale)');

    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $time = explode(' ', microtime());
    $startTime = $time[1] + $time[0];

    if (xarConfigVars::get(null,'Site.MLS.TranslationsBackend') == 'xml2php') {
        $l = xarLocaleGetInfo($locale);
        if ($l['charset'] == 'utf-8') {
            $ref_locale = $locale;
        } else {
            $l['charset'] = 'utf-8';
            $ref_locale = xarLocaleGetString($l);
        }
    } else {
        $ref_locale = $locale;
    }

    // Load core translations
    $backend = xarModAPIFunc('translations','admin','create_backend_instance',array('interface' => 'ReferencesBackend', 'locale' => $ref_locale));
    if (!isset($backend)) return;

    if (!$backend->bindDomain(XARMLS_DNTYPE_CORE, 'xaraya')) {
        $msg = xarML('Before generating translations you must first generate skels for locale #(1)', $ref_locale);
        $link = array(xarML('Click here to proceed.'), xarModURL('translations', 'admin', 'update_info', array('dntype' => 'core')));
        xarErrorSet(XAR_USER_EXCEPTION, 'MissingSkels', new DefaultUserException($msg, $link));
        return;
    }
    if (!$backend->loadContext('core:', 'core')) return;

    $gen = xarModAPIFunc('translations','admin','create_generator_instance',array('interface' => 'TranslationsGenerator', 'locale' => $locale));
    if (!isset($gen)) return;
    if (!$gen->bindDomain(XARMLS_DNTYPE_CORE, 'xaraya')) return;
    if (!$gen->create('core:', 'core')) return;

    $statistics['core'] = array('entries'=>0, 'keyEntries'=>0);

    while (list($string, $translation) = $backend->enumTranslations()) {
        $statistics['core']['entries']++;
        $gen->addEntry($string, $translation);
    }

    while (list($key, $translation) = $backend->enumKeyTranslations()) {
        $statistics['core']['keyEntries']++;
        $gen->addKeyEntry($key, $translation);
    }

    $gen->close();

    $time = explode(' ', microtime());
    $endTime = $time[1] + $time[0];

    return array('time' => $endTime - $startTime, 'statistics' => $statistics);
}

?>