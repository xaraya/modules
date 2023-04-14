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

function translations_adminapi_generate_core_trans($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert(isset($locale));

    // Security Check
    if(!xarSecurity::check('AdminTranslations')) return;

    $time = explode(' ', microtime());
    $startTime = $time[1] + $time[0];

    if (xarConfigVars::get(null,'Site.MLS.TranslationsBackend') == 'xml2php') {
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

    // Load core translations
    $backend = xarMod::apiFunc('translations','admin','create_backend_instance',array('interface' => 'ReferencesBackend', 'locale' => $ref_locale));
    if (!isset($backend)) return;

    if (!$backend->bindDomain(xarMLS::DNTYPE_CORE, 'xaraya')) {
        $msg = xarML('Before generating translations you must first generate skels for locale #(1)', $ref_locale);
        $link = array(xarML('Click here to proceed.'), xarController::URL('translations', 'admin', 'update_info', array('dntype' => 'core')));
        throw new Exception($msg);
    }
    if (!$backend->loadContext('core:', 'core')) return;

    $gen = xarMod::apiFunc('translations','admin','create_generator_instance',array('interface' => 'TranslationsGenerator', 'locale' => $locale));
    if (!isset($gen)) return;
    if (!$gen->bindDomain(xarMLS::DNTYPE_CORE, 'xaraya')) return;
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