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
 * generate translations XML skels for a specified module
 * @param $args['modid'] module registry identifier
 * @param $args['locale'] locale name
 * @return array statistics on generation process
 */
function translations_adminapi_generate_module_skels($args)
{
    // To prevent an error in safe mode, we supply a @, no other way i think
    @set_time_limit(0);

    // Get arguments
    extract($args);

    // Argument check
    assert(isset($modid) && isset($locale));

    if (!$modinfo = xarMod::getInfo($modid)) return;
    $modname = $modinfo['name'];
    $moddir = $modinfo['osdirectory'];

    // Security Check
    if(!xarSecurity::check('AdminTranslations')) return;

    // {ML_dont_parse 'modules/translations/class/PHPParser.php'}
    sys::import('modules.translations.class.PHPParser');
    // {ML_dont_parse 'modules/translations/class/TPLParser.php'}
    sys::import('modules.translations.class.TPLParser');

    $time = explode(' ', microtime());
    $startTime = $time[1] + $time[0];

    // Load core translations
    $core_backend = xarMod::apiFunc('translations','admin','create_backend_instance',array('interface' => 'ReferencesBackend', 'locale' => $locale));
    if (!isset($core_backend)) return;
    if (!$core_backend->bindDomain(xarMLS::DNTYPE_CORE, 'xaraya')) {
        $msg = xarML('Before you can generate skels for the #(1) module, you must first generate skels for the core.', $modname);
        $link = array(xarML('Click here to proceed.'), xarController::URL('translations', 'admin', 'update_info', array('dntype'=>'core')));
        throw new Exception($msg);
    }
    if (!$core_backend->loadContext('core:', 'core')) return;

    // Parse files
    $transEntriesCollection = array();
    $transKeyEntriesCollection = array();

    $subnames = xarMod::apiFunc('translations','admin','get_module_phpfiles',array('moddir'=>$moddir));

    $module_contexts_list = array();
    foreach ($subnames as $subname) {
        $module_contexts_list[] = 'modules:'.$modname.'::'.$subname;
        $filename = sys::code() . "modules/$moddir/xar$subname.php";

        if (file_exists($filename)) {
            $parser = new PHPParser();
            $parser->parse($filename);

            $transEntriesCollection[$subname] = $parser->getTransEntries();
            $transKeyEntriesCollection[$subname] = $parser->getTransKeyEntries();
        }

    }

    $dirnames = xarMod::apiFunc('translations','admin','get_module_dirs',array('moddir'=>$moddir));
    xarLog::variable('dirnames',$dirnames);
    foreach ($dirnames as $dirname) {
        ${$dirname . "names"} = array();
        if (!preg_match('!^templates!i', $dirname, $matches)) {
            $pattern = '/^([a-z0-9\-_]+)\.php$/i';
            $xtype = 'php';
        }
        else {
            $pattern = '/^([a-z0-9\-_]+)\.xt$/i';
            $xtype = 'xt';
        }
        $subnames = xarMod::apiFunc('translations','admin','get_module_files',
                         array('moddir'=>sys::code() . "modules/$moddir/xar$dirname",'pattern'=>$pattern));
        xarLog::variable('subnames',$subnames);
        foreach ($subnames as $subname) {
            $module_contexts_list[] = 'modules:'.$modname.':'.$dirname.':'.$subname;
            if ($xtype == 'xt') $parser = new TPLParser();
            else $parser = new PHPParser();
            $parser->parse(sys::code() . "modules/$moddir/xar$dirname/$subname.$xtype");
            ${$dirname . "names"}[] = $subname;
            $transEntriesCollection[$dirname.'::'.$subname] = $parser->getTransEntries();
            $transKeyEntriesCollection[$dirname.'::'.$subname] = $parser->getTransKeyEntries();
        }
    }

    $transEntriesCollection = module_translations_gather_common_entries($transEntriesCollection);
    $transKeyEntriesCollection = module_translations_gather_common_entries($transKeyEntriesCollection);

    $subnames[] = 'common';
    // Load previously made translations
    $backend = xarMod::apiFunc('translations','admin','create_backend_instance',array('interface' => 'ReferencesBackend', 'locale' => $locale));
    if (!isset($backend)) return;

    if ($backend->bindDomain(xarMLS::DNTYPE_MODULE,$modname)) {
        if ($backend->hasContext('modules:','common')){
            if (!$backend->loadContext('modules:','common')) return;
        }
        foreach ($module_contexts_list as $module_context) {
            list ($dntype1, $dnname1, $ctxtype1, $ctxname1) = explode(':',$module_context);
            if ($backend->hasContext('modules:'.$ctxtype1,$ctxname1)){
                if (!$backend->loadContext('modules:'.$ctxtype1,$ctxname1)) return;
            }
        }
    }

    // Load KEYS
    $filename = sys::code() . "modules/$moddir/KEYS";
    $KEYS = array();
    if (file_exists($filename)) {
        $lines = file($filename);
        foreach ($lines as $line) {
            if ($line{0} == '#') continue;
            list($key, $value) = explode('=', $line);
            $key = trim($key);
            $value = trim($value);
            $KEYS[$key] = $value;
        }
    }

    // Create skels
    $subnames = array_keys($transEntriesCollection);
    if (xarConfigVars::get(null,'Site.MLS.TranslationsBackend') == 'xml2php') {
        if (!$parsedLocale = xarMLS::parseLocaleString($locale)) return false;
        $genLocale = $parsedLocale['lang'].'_'.$parsedLocale['country'].'.utf-8';
    } else {
        $genLocale = $locale;
    }

    $gen = xarMod::apiFunc('translations','admin','create_generator_instance',array('interface' => 'ReferencesGenerator', 'locale' => $genLocale));
    if (!isset($gen)) return;
    if (!$gen->bindDomain(xarMLS::DNTYPE_MODULE, $modname)) return;

    foreach ($subnames as $subname) {
        if (preg_match('/(.*)::(.*)/', $subname, $matches)) {
           list ($ctxtype1, $ctxname1) = explode('::',$subname);
        } else {
            $ctxtype1 = '';
            $ctxname1 = $subname;
        }

        $fileAlreadyOpen = false;

        $statistics[$subname] = array('entries'=>0, 'keyEntries'=>0);

        // Avoid creating entries for the same locale
        if ($locale != 'en_US.utf-8') {
            foreach ($transEntriesCollection[$subname] as $string => $references) {

                // Check if string appears in core translations
                $entry = $core_backend->getEntry($string);
                if (isset($entry)) continue;

                $statistics[$subname]['entries']++;
                // Get previous translation, it's void if not yet translated
                $translation = $backend->translate($string);
                $marked = $backend->markEntry($string);

                if (!$fileAlreadyOpen) {
                    if (!$gen->create('modules:'.$ctxtype1,$ctxname1)) return;
                    $fileAlreadyOpen = true;
                }
                // Add entry
                $gen->addEntry($string, $references, $translation);
            }
        }

        foreach ($transKeyEntriesCollection[$subname] as $key => $references) {

            // Check if key appears in core translations
            $keyEntry = $core_backend->getEntryByKey($key);
            if (isset($keyEntry)) continue;

            $statistics[$subname]['keyEntries']++;
            // Get previous translation, it's void if not yet translated
            $translation = $backend->translateByKey($key);
            $marked = $backend->markEntryByKey($key);
            // Get the original translation made by developer if any
            if (!$translation && isset($KEYS[$key])) $translation = $KEYS[$key];

            if (!$fileAlreadyOpen) {
                if (!$gen->create('modules:'.$ctxtype1,$ctxname1)) return;
                $fileAlreadyOpen = true;
            }
            // Add key entry
            $gen->addKeyEntry($key, $references, $translation);
        }

        if ($fileAlreadyOpen) {
            $gen->close();
        } else {
            $gen->deleteIfExists('modules:'.$ctxtype1,$ctxname1);
        }
    }
    if (!$gen->open('modules:','fuzzy')) return;
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
function module_translations_gather_common_entries($transEntriesCollection)
{
    $commonEntries = array();
    $subnames = array_keys($transEntriesCollection);
    foreach ($subnames as $subname) {
        foreach ($transEntriesCollection[$subname] as $string => $references) {

            $refs_inserted = false;
            foreach ($subnames as $other_subname) {
                if ($other_subname == $subname) continue;

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

                    unset($transEntriesCollection[$subname][$string]);
                    unset($transEntriesCollection[$other_subname][$string]);
                }
            }
        }
    }
    $transEntriesCollection['common'] = $commonEntries;
    return $transEntriesCollection;
}

?>