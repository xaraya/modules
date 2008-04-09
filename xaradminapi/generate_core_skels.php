<?php
/**
 * Generate XML skeletons admin api function
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * generate translations XML skels for the core
 * @param $args['locale'] locale name
 * @returns array
 * @return statistics on generation process
 */
function translations_adminapi_generate_core_skels($args)
{
    // To suppress an error in safe mode we supply a @, no other way i think.
    @set_time_limit(0);

    // Get arguments
    extract($args);

    // Argument check
    assert('isset($locale)');

    if(!xarSecurityCheck('AdminTranslations')) return;

    // {ML_dont_parse 'modules/translations/class/PHPParser.php'}
    sys::import('modules.translations.class.PHPParser');
    // {ML_dont_parse 'modules/translations/class/TPLParser.php'}
    sys::import('modules.translations.class.TPLParser');

    $time = explode(' ', microtime());
    $startTime = $time[1] + $time[0];

    $transEntriesCollection = array();
    $transKeyEntriesCollection = array();

    $filename = 'index.php';

    $parser = new PHPParser();
    $parser->parse($filename);

    $transEntries = $parser->getTransEntries();
    $transKeyEntries = $parser->getTransKeyEntries();

    // Load core translations
    $core_backend = xarModAPIFunc('translations','admin','create_backend_instance',array('interface' => 'ReferencesBackend', 'locale' => $locale));
    if (!isset($core_backend)) return;
    if ($core_backend->bindDomain(XARMLS_DNTYPE_CORE) &&
        !$core_backend->loadContext('core:', 'core')) return;

    // Generate translations skels
    if (xarConfigVars::get(null,'Site.MLS.TranslationsBackend') == 'xml2php') {
       if (!$parsedLocale = xarMLS__parseLocaleString($locale)) return false;
       $genLocale = $parsedLocale['lang'].'_'.$parsedLocale['country'].'.utf-8';
    } else {
       $genLocale = $locale;
    }

    $gen = xarModAPIFunc('translations','admin','create_generator_instance',array('interface' => 'ReferencesGenerator', 'locale' => $genLocale));
    if (!isset($gen)) return;
    if (!$gen->bindDomain(XARMLS_DNTYPE_CORE)) return;
    if (!$gen->create('core:', 'core')) return;

    $statistics['core'] = array('entries'=>0, 'keyEntries'=>0);

    // Avoid creating entries for the same locale (en_US.utf-8)
    // NOTE from voll: I comment this IF because we don't have translation anyway
    //    if ($locale != 'en_US.utf-8') {

    foreach ($transEntries as $string => $references) {
        $statistics['core']['entries']++;
        // Get previous translation, it's void if not yet translated
        $translation = $core_backend->translate($string);
        $marked = $core_backend->markEntry($string);
        $gen->addEntry($string, $references, $translation);
    }
    //    }

    foreach ($transKeyEntries as $key => $references) {
        $statistics['core']['keyEntries']++;
        // Get previous translation, it's void if not yet translated
        $translation = $core_backend->translateByKey($key);
        $marked = $core_backend->markEntryByKey($key);
        $gen->addKeyEntry($key, $references, $translation);
    }

    $gen->close();

    if (!$gen->open('core:','fuzzy')) return;
    $fuzzyEntries = $core_backend->getFuzzyEntries();
    foreach ($fuzzyEntries as $ind => $fuzzyEntry) {
        // Add entry
        $gen->addEntry($fuzzyEntry['string'], $fuzzyEntry['references'], $fuzzyEntry['translation']);
    }
    $fuzzyKeys = $core_backend->getFuzzyEntriesByKey();
    foreach ($fuzzyKeys as $ind => $fuzzyKey) {
        // Add entry
        $gen->addKeyEntry($fuzzyKey['key'], $fuzzyKey['references'], $fuzzyKey['translation']);
    }
    $gen->close();

    $time = explode(' ', microtime());
    $endTime = $time[1] + $time[0];

    return array('time' => $endTime - $startTime, 'statistics' => $statistics);
}

?>