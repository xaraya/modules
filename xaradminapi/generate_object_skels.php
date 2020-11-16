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
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */

/**
 * generate translations XML skels for a specified theme
 * @param $args['objectid'] object identifier
 * @param $args['locale'] locale name
 * @returns array
 * @return statistics on generation process
 */
function translations_adminapi_generate_object_skels($args)
{
    // To suppress an error in safe mode we supply a @ here, no other way I think
    @set_time_limit(0);

    // Get arguments
    extract($args);

    // Argument check
    assert('isset($objectid) && isset($locale)');

    $tplData['object'] = xarMod::apiFunc('dynamicdata', 'user', 'getobjectlist', array('objectid' => $objectid));
    if (!is_object($tplData['object'])) {
        return;
    }
    $objectlabel = $tplData['object']->label;
    $objectname = $tplData['object']->name;

    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) {
        return;
    }

    // {ML_dont_parse 'modules/translations/class/TPLParser.php'}
    sys::import('modules.translations.class.TPLParser');

    $time = explode(' ', microtime());
    $startTime = $time[1] + $time[0];

    // Load core translations
    $core_backend = xarMod::apiFunc('translations', 'admin', 'create_backend_instance', array('interface' => 'ReferencesBackend', 'locale' => $locale));
    if (!isset($core_backend)) {
        return;
    }
    if (!$core_backend->bindDomain(xarMLS::DNTYPE_CORE, 'xaraya')) {
        $msg = xarML('Before you can generate skels for the #(1) object, you must first generate skels for the core.', $objectname);
        $link = array(xarML('Click here to proceed.'), xarModURL('translations', 'admin', 'update_info', array('dntype'=>'core')));
        throw new Exception($msg);
    }
    if (!$core_backend->loadContext('core:', 'core')) {
        return;
    }

    // Get the properties that are translatable
    $transEntriesCollection = array();
    $transKeyEntriesCollection = array();
    $object_contexts_list = array();

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
    // Force regeneration of the field list
    $fieldlist = $tplData['object']->getFieldList(1);

    // Get the items of translatable properties
    $items = $tplData['object']->getItems();

    $translation_enries = array();
    $prefix = 'objects/'.$objectname;
    foreach ($items as $item) {
        foreach ($item as $key => $element) {
            // Don't try and translate the ID fields. We just need them as a reference
            if ($key == 'id') {
                continue;
            }
            
            $transEntriesCollection[$objectname.'::'.$key][$element][] = array('line' => $item['id'], 'file' => $objectname.'::'.$key);
            $transKeyEntriesCollection[$objectname.'::'.$key] = array();
        }
    }
        
    $transEntriesCollection = object_translations_gather_common_entries($transEntriesCollection);
    $transKeyEntriesCollection = object_translations_gather_common_entries($transKeyEntriesCollection);

    $subnames[] = 'common';
    // Load previously made translations
    $backend = xarMod::apiFunc('translations', 'admin', 'create_backend_instance', array('interface' => 'ReferencesBackend', 'locale' => $locale));
    if (!isset($backend)) {
        return;
    }

    if ($backend->bindDomain(xarMLS::DNTYPE_OBJECT, $objectname)) {
        if ($backend->hasContext('objects:', 'common')) {
            if (!$backend->loadContext('objects:', 'common')) {
                return;
            }
        }
        foreach ($object_contexts_list as $object_context) {
            list($dntype1, $dnname1, $ctxtype1, $ctxname1) = explode(':', $object_context);
            if ($backend->hasContext('objects:'.$ctxtype1, $ctxname1)) {
                if (!$backend->loadContext('objects:'.$ctxtype1, $ctxname1)) {
                    return;
                }
            }
        }
    }

    // Load KEYS
    $filename = "objects/$objectname/KEYS";
    $KEYS = array();
    if (file_exists($filename)) {
        $lines = file($filename);
        foreach ($lines as $line) {
            if ($line{0} == '#') {
                continue;
            }
            list($key, $value) = explode('=', $line);
            $key = trim($key);
            $value = trim($value);
            $KEYS[$key] = $value;
        }
    }

    // Create skels
    $subnames = array_keys($transEntriesCollection);
    if (xarConfigVars::get(null, 'Site.MLS.TranslationsBackend') == 'xml2php') {
        if (!$parsedLocale = xarMLS__parseLocaleString($locale)) {
            return false;
        }
        $genLocale = $parsedLocale['lang'].'_'.$parsedLocale['country'].'.utf-8';
    } else {
        $genLocale = $locale;
    }

    $gen = xarMod::apiFunc('translations', 'admin', 'create_generator_instance', array('interface' => 'ReferencesGenerator', 'locale' => $genLocale));
    if (!isset($gen)) {
        return;
    }
    if (!$gen->bindDomain(xarMLS::DNTYPE_OBJECT, $objectname)) {
        return;
    }

    foreach ($subnames as $subname) {
        if (preg_match('/(.*)::(.*)/', $subname, $matches)) {
            list($ctxtype1, $ctxname1) = explode('::', $subname);
        } else {
            $ctxtype1 = '';
            $ctxname1 = $subname;
        }

        $statistics[$subname] = array('entries'=>0, 'keyEntries'=>0);

        $fileAlreadyOpen = false;
        // Avoid creating entries for the same locale
        if ($locale != 'en_US.utf-8') {
            foreach ($transEntriesCollection[$subname] as $string => $references) {

                // Check if string appears in core translations
                $entry = $core_backend->getEntry($string);
                if (isset($entry)) {
                    continue;
                }

                // There is no core translation: up the number of entries
                $statistics[$subname]['entries']++;
                
                // Get previous translation, it's empty if not yet translated
                $translation = $backend->translate($string);
                $marked = $backend->markEntry($string);
                
                if (!$fileAlreadyOpen) {
                    if (!$gen->create('objects:'.$ctxtype1, $ctxname1)) {
                        return;
                    }
                    $fileAlreadyOpen = true;
                }
                // Add entry
                $gen->addEntry($string, $references, $translation);
            }
        }

        foreach ($transKeyEntriesCollection[$subname] as $key => $references) {

            // Check if key appears in core translations
            $keyEntry = $core_backend->getEntryByKey($key);
            if (isset($keyEntry)) {
                continue;
            }

            $statistics[$subname]['keyEntries']++;
            // Get previous translation, it's void if not yet translated
            $translation = $backend->translateByKey($key);
            $marked = $backend->markEntryByKey($key);

            // Get the original translation made by developer if any
            if (!$translation && isset($KEYS[$key])) {
                $translation = $KEYS[$key];
            }

            if (!$fileAlreadyOpen) {
                if (!$gen->create('objects:'.$ctxtype1, $ctxname1)) {
                    return;
                }
                $fileAlreadyOpen = true;
            }
            // Add key entry
            $gen->addKeyEntry($key, $references, $translation);
        }

        if ($fileAlreadyOpen) {
            $gen->close();
        } else {
            $gen->deleteIfExists('objects:'.$ctxtype1, $ctxname1);
        }
    }
    if (!$gen->open('objects:', 'fuzzy')) {
        return;
    }
    $fuzzyEntries = $backend->getFuzzyEntries();
    foreach ($fuzzyEntries as $ind => $fuzzyEntry) {
        // Add entry
        $gen->addEntry($fuzzyEntry['string'], $fuzzyEntry['references'], $fuzzyEntry['translation']);
    }
    $fuzzyKeys = $backend->getFuzzyEntriesByKey();
    foreach ($fuzzyKeys as $ind => $fuzzyKey) {
        // Add entry
        $gen->addKeyEntry($fuzzyKey['key'], $fuzzyKey['references'], $fuzzyKey['translation']);
    }
    $gen->close();

    $time = explode(' ', microtime());
    $endTime = $time[1] + $time[0];
    return array('time' => $endTime - $startTime, 'statistics' => $statistics);
}

/* PRIVATE FUNCTIONS */
function object_translations_gather_common_entries($transEntriesCollection)
{
    $commonEntries = array();
    $subnames = array_keys($transEntriesCollection);
    foreach ($subnames as $subname) {
        foreach ($transEntriesCollection[$subname] as $string => $references) {
            $refs_inserted = false;
            foreach ($subnames as $other_subname) {
                if ($other_subname == $subname) {
                    continue;
                }

                if (isset($transEntriesCollection[$other_subname][$string])) {
                    // Found a duplicated ML string
                    if (!isset($commonEntries[$string])) {
                        $commonEntries[$string] = array();
                    }

                    if (!$refs_inserted) {
                        // Insert once the references in $transEntriesCollection[$subname][$string]
                        foreach ($references as $reference) {
                            $ref_exists = false;
                            foreach ($commonEntries[$string] as $existant_refs) {
                                if ($reference['file'] == $existant_refs['file'] &&
                                    $reference['line'] == $existant_refs['line']) {
                                    $ref_exists = true;
                                }
                            }
                            if (!$ref_exists) {
                                $commonEntries[$string][] = $reference;
                            }
                        }
                        $refs_inserted = true;
                    }

                    // Insert the references in $transEntriesCollection[$other_subname][$string]
                    $other_references = $transEntriesCollection[$other_subname][$string];
                    foreach ($other_references as $reference) {
                        $ref_exists = false;
                        foreach ($commonEntries[$string] as $existant_refs) {
                            if ($reference['file'] == $existant_refs['file'] &&
                                $reference['line'] == $existant_refs['line']) {
                                $ref_exists = true;
                            }
                        }
                        if (!$ref_exists) {
                            $commonEntries[$string][] = $reference;
                        }
                    }

                    // FIXME: This is a workaround for bug #2423, not a fix
                    unset($transEntriesCollection[$subname][$string]);
                    unset($transEntriesCollection[$other_subname][$string]);
                }
            }
        }
    }
    $transEntriesCollection['common'] = $commonEntries;
    return $transEntriesCollection;
}
