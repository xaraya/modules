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

/* EVENT */function translations_adminevt_OnModLoad($args)
{
    if (xarMLS::getMode() != xarMLS::UNBOXED_MULTI_LANGUAGE_MODE) {
        $msg = xarML('To execute the translations module you must set the Multi Language System mode to UNBOXED.');
        throw new Exception($msg);
    }
    xarTpl::setPageTitle(xarML('Welcome to translators\' paradise!'));
}

// PRIVATE STUFF

// druidbar (upper)
define('CHOOSE', 0);
define('INFO', 1);
define('GENSKELS', 2);
define('TRAN', 3);
define('DELFUZZY', 4);
define('GENTRANS', 5);
define('REL', 6);
define('DOWNLOAD', 7);

// TODO make code more elegant (waiting for full features realization)
function &translations_create_druidbar($currentStep, $dnType, $dnName, $extid)
{
    $urlarray = ['dnType'=>$dnType, 'dnName'=>$dnName, 'extid'=>$extid];
    $stepCount = 0;

    switch ($dnType) {
        case xarMLS::DNTYPE_CORE:
            $stepLabels[CHOOSE] = xarML('Core');
            $stepLabels[INFO] = xarML('Overview');
            $stepLabels[GENSKELS] = xarML('Skel. Generation');
            $stepLabels[TRAN] = xarML('Translate');
            $stepLabels[DELFUZZY] = xarML('Delete fuzzy');
            $stepLabels[GENTRANS] = xarML('Trans. Generation');
            $stepLabels[REL] = xarML('Release');
            $stepLabels[DOWNLOAD] = xarML('Download');

            $stepURLs[CHOOSE] = xarController::URL('translations', 'admin', 'core_overview', $urlarray);
            $stepURLs[INFO] = xarController::URL('translations', 'admin', 'core_overview', $urlarray);
            $stepURLs[GENSKELS] = xarController::URL('translations', 'admin', 'generate_skels_info', $urlarray);
            $stepURLs[TRAN] = xarController::URL('translations', 'admin', 'translate', $urlarray);
            $stepURLs[DELFUZZY] = xarController::URL('translations', 'admin', 'delete_fuzzy', $urlarray);
            $stepURLs[GENTRANS] = xarController::URL('translations', 'admin', 'generate_trans_info', $urlarray);
            $stepURLs[REL] = xarController::URL('translations', 'admin', 'release_info', $urlarray);

            $stepCount = $currentStep + 1;

            break;
        case xarMLS::DNTYPE_MODULE:
            $stepLabels[CHOOSE] = xarML('Choose a module');
            $stepLabels[INFO] = xarML('Overview');
            $stepLabels[GENSKELS] = xarML('Skel. Generation');
            $stepLabels[TRAN] = xarML('Translate');
            $stepLabels[DELFUZZY] = xarML('Delete fuzzy');
            $stepLabels[GENTRANS] = xarML('Trans. Generation');
            $stepLabels[REL] = xarML('Release');
            $stepLabels[DOWNLOAD] = xarML('Download');

            $stepURLs[CHOOSE] = xarController::URL('translations', 'admin', 'choose_a_module');
            $stepURLs[INFO] = xarController::URL('translations', 'admin', 'module_overview', $urlarray);
            $stepURLs[GENSKELS] = xarController::URL('translations', 'admin', 'generate_skels_info', $urlarray);
            $stepURLs[TRAN] = xarController::URL('translations', 'admin', 'translate', $urlarray);
            $stepURLs[DELFUZZY] = xarController::URL('translations', 'admin', 'delete_fuzzy', $urlarray);
            $stepURLs[GENTRANS] = xarController::URL('translations', 'admin', 'generate_trans_info', $urlarray);
            $stepURLs[REL] = xarController::URL('translations', 'admin', 'release_info', $urlarray);

            $stepCount = $currentStep + 1;

            break;
        case xarMLS::DNTYPE_PROPERTY:
            $stepLabels[CHOOSE] = xarML('Choose a property');
            $stepLabels[INFO] = xarML('Overview');
            $stepLabels[GENSKELS] = xarML('Skel. Generation');
            $stepLabels[TRAN] = xarML('Translate');
            $stepLabels[DELFUZZY] = xarML('Delete fuzzy');
            $stepLabels[GENTRANS] = xarML('Trans. Generation');
            $stepLabels[REL] = xarML('Release');
            $stepLabels[DOWNLOAD] = xarML('Download');

            $stepURLs[CHOOSE] = xarController::URL('translations', 'admin', 'choose_a_property');
            $stepURLs[INFO] = xarController::URL('translations', 'admin', 'theme_overview', $urlarray);
            $stepURLs[GENSKELS] = xarController::URL('translations', 'admin', 'generate_skels_info', $urlarray);
            $stepURLs[TRAN] = xarController::URL('translations', 'admin', 'translate', $urlarray);
            $stepURLs[DELFUZZY] = xarController::URL('translations', 'admin', 'delete_fuzzy', $urlarray);
            $stepURLs[GENTRANS] = xarController::URL('translations', 'admin', 'generate_trans_info', $urlarray);
            $stepURLs[REL] = xarController::URL('translations', 'admin', 'release_info', $urlarray);

            $stepCount = $currentStep + 1;

            break;
        case xarMLS::DNTYPE_BLOCK:
            $stepLabels[CHOOSE] = xarML('Choose a block');
            $stepLabels[INFO] = xarML('Overview');
            $stepLabels[GENSKELS] = xarML('Skel. Generation');
            $stepLabels[TRAN] = xarML('Translate');
            $stepLabels[DELFUZZY] = xarML('Delete fuzzy');
            $stepLabels[GENTRANS] = xarML('Trans. Generation');
            $stepLabels[REL] = xarML('Release');
            $stepLabels[DOWNLOAD] = xarML('Download');

            $stepURLs[CHOOSE] = xarController::URL('translations', 'admin', 'choose_a_block');
            $stepURLs[INFO] = xarController::URL('translations', 'admin', 'bock_overview', $urlarray);
            $stepURLs[GENSKELS] = xarController::URL('translations', 'admin', 'generate_skels_info', $urlarray);
            $stepURLs[TRAN] = xarController::URL('translations', 'admin', 'translate', $urlarray);
            $stepURLs[DELFUZZY] = xarController::URL('translations', 'admin', 'delete_fuzzy', $urlarray);
            $stepURLs[GENTRANS] = xarController::URL('translations', 'admin', 'generate_trans_info', $urlarray);
            $stepURLs[REL] = xarController::URL('translations', 'admin', 'release_info', $urlarray);

            $stepCount = $currentStep + 1;

            break;
        case xarMLS::DNTYPE_THEME:
            $stepLabels[CHOOSE] = xarML('Choose a theme');
            $stepLabels[INFO] = xarML('Overview');
            $stepLabels[GENSKELS] = xarML('Skel. Generation');
            $stepLabels[TRAN] = xarML('Translate');
            $stepLabels[DELFUZZY] = xarML('Delete fuzzy');
            $stepLabels[GENTRANS] = xarML('Trans. Generation');
            $stepLabels[REL] = xarML('Release');
            $stepLabels[DOWNLOAD] = xarML('Download');

            $stepURLs[CHOOSE] = xarController::URL('translations', 'admin', 'choose_a_theme');
            $stepURLs[INFO] = xarController::URL('translations', 'admin', 'theme_overview', $urlarray);
            $stepURLs[GENSKELS] = xarController::URL('translations', 'admin', 'generate_skels_info', $urlarray);
            $stepURLs[TRAN] = xarController::URL('translations', 'admin', 'translate', $urlarray);
            $stepURLs[DELFUZZY] = xarController::URL('translations', 'admin', 'delete_fuzzy', $urlarray);
            $stepURLs[GENTRANS] = xarController::URL('translations', 'admin', 'generate_trans_info', $urlarray);
            $stepURLs[REL] = xarController::URL('translations', 'admin', 'release_info', $urlarray);

            $stepCount = $currentStep + 1;

            break;
        case xarMLS::DNTYPE_OBJECT:
            $stepLabels[CHOOSE] = xarML('Choose a dataobject');
            $stepLabels[INFO] = xarML('Overview');
            $stepLabels[GENSKELS] = xarML('Skel. Generation');
            $stepLabels[TRAN] = xarML('Translate');
            $stepLabels[DELFUZZY] = xarML('Delete fuzzy');
            $stepLabels[GENTRANS] = xarML('Trans. Generation');
            $stepLabels[REL] = xarML('Release');
            $stepLabels[DOWNLOAD] = xarML('Download');

            $stepURLs[CHOOSE] = xarController::URL('translations', 'admin', 'choose_a_object');
            $stepURLs[INFO] = xarController::URL('translations', 'admin', 'object_overview', $urlarray);
            $stepURLs[GENSKELS] = xarController::URL('translations', 'admin', 'generate_skels_info', $urlarray);
            $stepURLs[TRAN] = xarController::URL('translations', 'admin', 'translate', $urlarray);
            $stepURLs[DELFUZZY] = xarController::URL('translations', 'admin', 'delete_fuzzy', $urlarray);
            $stepURLs[GENTRANS] = xarController::URL('translations', 'admin', 'generate_trans_info', $urlarray);
            $stepURLs[REL] = xarController::URL('translations', 'admin', 'release_info', $urlarray);

            $stepCount = $currentStep + 1;

            break;
    }
    $steps=[];

    $steps= [
        'stepLabels'=>$stepLabels,
        'stepURLs'=>$stepURLs,
        'currentStep'=>$currentStep,
        'stepCount'=>$stepCount, ];
    return $steps;
}

// opbar (lower)
define('OVERVIEW', 0);
define('GEN_SKELS', 1);
define('TRANSLATE', 2);
define('DEL_FUZZY', 3);
define('GEN_TRANS', 4);
define('RELEASE', 5);

function &translations_create_opbar($currentOp, $dnType, $dnName, $extid)
{
    $urlarray = ['dnType'=>$dnType, 'dnName'=>$dnName, 'extid'=>$extid];
    // Overview | Generate skels | Translate | Delete fuzzy | Generate translations | Release package
    $opLabels[OVERVIEW] = xarML('Overview');
    switch ($dnType) {
        case xarMLS::DNTYPE_CORE:
            $opURLs[OVERVIEW] = xarController::URL('translations', 'admin', 'core_overview', $urlarray);
            break;
        case xarMLS::DNTYPE_MODULE:
            $opURLs[OVERVIEW] = xarController::URL('translations', 'admin', 'module_overview', $urlarray);
            break;
        case xarMLS::DNTYPE_PROPERTY:
            $opURLs[OVERVIEW] = xarController::URL('translations', 'admin', 'property_overview', $urlarray);
            break;
        case xarMLS::DNTYPE_BLOCK:
            $opURLs[OVERVIEW] = xarController::URL('translations', 'admin', 'block_overview', $urlarray);
            break;
        case xarMLS::DNTYPE_THEME:
            $opURLs[OVERVIEW] = xarController::URL('translations', 'admin', 'theme_overview', $urlarray);
            break;
        case xarMLS::DNTYPE_OBJECT:
            $opURLs[OVERVIEW] = xarController::URL('translations', 'admin', 'object_overview', $urlarray);
            break;
    }
    $opLabels[GEN_SKELS] = xarML('Generate skels');
    $opURLs[GEN_SKELS] = xarController::URL('translations', 'admin', 'generate_skels_info', $urlarray);
    $opLabels[TRANSLATE] = xarML('Translate');
    $opURLs[TRANSLATE] = xarController::URL('translations', 'admin', 'translate', $urlarray);
    $opLabels[DEL_FUZZY] = xarML('Delete fuzzy');
    $opURLs[DEL_FUZZY] = xarController::URL('translations', 'admin', 'delete_fuzzy', $urlarray);
    $opLabels[GEN_TRANS] = xarML('Generate translations');
    $opURLs[GEN_TRANS] = xarController::URL('translations', 'admin', 'generate_trans_info', $urlarray);
    $opLabels[RELEASE] = xarML('Release package');
    $opURLs[RELEASE] = xarController::URL('translations', 'admin', 'release_info', $urlarray);

    // Enables See module details & Generate translations skels
    $enabledOps = [true, true, false, false, false];

    $locale = translations_working_locale();
    $args['interface'] = 'ReferencesBackend';
    $args['locale'] = $locale;
    $backend = xarMod::apiFunc('translations', 'admin', 'create_backend_instance', $args);
    if (!isset($backend)) {
        return;
    }

    if ($backend->bindDomain($dnType, $dnName)) {
        $enabledOps[TRANSLATE] = true; // Enables Translate
        $enabledOps[DEL_FUZZY] = true; // Enables Translate
        $enabledOps[GEN_TRANS] = false; // Enables Generate translations
        $args['interface'] = 'TranslationsBackend';
        $args['locale'] = $locale;
        $backend = xarMod::apiFunc('translations', 'admin', 'create_backend_instance', $args);
        if (!isset($backend)) {
            return;
        }
        if ($backend->bindDomain($dnType, $dnName)) {
            // Enables Release translations package
            $enabledOps[RELEASE] = false;
        }
    }
    $opsData =[];
    $opsData = ['opLabels'=>$opLabels, 'opURLs'=>$opURLs, 'enabledOps'=>$enabledOps, 'currentOp'=>$currentOp];
    return $opsData;
}

function translations_create_trabar($dnType, $dnName, $extid, $subtype, $subname, $backend=null)
{
    @set_time_limit(0);
    $time = explode(' ', microtime());
    $startTime = $time[1] + $time[0];

    // Security Check
    if (!xarSecurity::check('ReadTranslations')) {
        return;
    }

    $currentTra = -1;
    switch ($dnType) {
        case xarMLS::DNTYPE_CORE:
            $subtypes = [];
            $subnames = [];
            $entrydata = [];

            $args = [];
            $args['dntype'] = xarMLS::DNTYPE_CORE;
            $args['dnname'] = 'xaraya';
            $args['subtype'] = 'core:';
            $args['subname'] = 'core';
            $selectedsubtype = 'core:';
            $selectedsubname = 'core';
            $entry = xarMod::apiFunc('translations', 'admin', 'getcontextentries', $args);
            if ($entry['numEntries']+$entry['numKeyEntries'] > 0) {
                $entrydata[] = $entry;
                $subtypes[] = 'core:';
                $subnames[] = 'core';
            }
            break;

        case xarMLS::DNTYPE_MODULE:

            $modid = $extid;

            if (!$modinfo = xarMod::getInfo($modid)) {
                return;
            }
            $modname = $modinfo['name'];
            $moddir = $modinfo['osdirectory'];

            $selectedsubtype = $subtype;
            $selectedsubname = $subname;

            $module_contexts_list[] = 'modules:'.$modname.'::common';

            $subnames = xarMod::apiFunc('translations', 'admin', 'get_module_phpfiles', ['moddir'=>$moddir]);
            foreach ($subnames as $subname) {
                $module_contexts_list[] = 'modules:'.$modname.'::'.$subname;
            }

            $dirnames = xarMod::apiFunc('translations', 'admin', 'get_module_dirs', ['moddir'=>$moddir]);

            foreach ($dirnames as $dirname) {
                if (!preg_match('%templates%i', $dirname, $matches)) {
                    $pattern = '/^([a-z0-9\-_]+)\.php$/i';
                } else {
                    $pattern = '/^([a-z0-9\-_]+)\.xt$/i';
                }
                $subnames = xarMod::apiFunc(
                    'translations',
                    'admin',
                    'get_module_files',
                    ['moddir' => sys::code() . "modules/$moddir/xar$dirname",'pattern'=>$pattern]
                );

                foreach ($subnames as $subname) {
                    $module_contexts_list[] = 'modules:'.$modname.':'.$dirname.':'.$subname;
                }
            }
            $subtypes = [];
            $subnames = [];
            $entrydata = [];
            foreach ($module_contexts_list as $module_context) {
                [$dntype1, $dnname1, $ctxtype1, $ctxname1] = explode(':', $module_context);
                $args = [];
                $ctxtype2 = 'modules:'.$ctxtype1;
                $args['dntype'] = xarMLS::DNTYPE_MODULE;
                $args['dnname'] = $dnname1;
                $args['subtype'] = $ctxtype2;
                $args['subname'] = $ctxname1;
                $entry = xarMod::apiFunc('translations', 'admin', 'getcontextentries', $args);
                if ($entry['numEntries']+$entry['numKeyEntries'] > 0) {
                    $entrydata[] = $entry;
                    $subtypes[] = $ctxtype2;
                    $subnames[] = $ctxname1;
                }
            }
            break;

        case xarMLS::DNTYPE_PROPERTY:

            xarMod::apiLoad('dynamicdata');
            $tables =& xarDB::getTables();
            sys::import('xaraya.structures.query');
            $q = new Query('SELECT', $tables['dynamic_properties_def']);
            $q->eq('id', $extid);
            $q->run();
            $propertyinfo = $q->row();

            $propertyname = $propertyinfo['name'];
            $propertydir = $propertyinfo['name'];

            $selectedsubtype = $subtype;
            $selectedsubname = $subname;

            $property_contexts_list[] = 'properties:'.$propertyname.'::common';

            $subnames = xarMod::apiFunc('translations', 'admin', 'get_property_phpfiles', ['propertydir'=>$propertydir]);
            foreach ($subnames as $subname) {
                $property_contexts_list[] = 'properties:'.$propertyname.'::'.$subname;
            }

            $dirnames = xarMod::apiFunc('translations', 'admin', 'get_property_dirs', ['propertydir'=>$propertydir]);
            foreach ($dirnames as $dirname) {
                if (!preg_match('!^templates!i', $dirname, $matches)) {
                    $pattern = '/^([a-z0-9\-_]+)\.php$/i';
                } else {
                    $pattern = '/^([a-z0-9\-_]+)\.xt$/i';
                }
                $subnames = xarMod::apiFunc(
                    'translations',
                    'admin',
                    'get_property_files',
                    ['propertydir'=>sys::code() . "properties/$propertydir/xar$dirname",'pattern'=>$pattern]
                );
                foreach ($subnames as $subname) {
                    $property_contexts_list[] = 'properties:'.$propertyname.':'.$dirname.':'.$subname;
                }
            }

            $subtypes = [];
            $subnames = [];
            $entrydata = [];
            foreach ($property_contexts_list as $property_context) {
                [$dntype1, $dnname1, $ctxtype1, $ctxname1] = explode(':', $property_context);
                $args = [];
                $ctxtype2 = 'properties:'.$ctxtype1;
                $args['dntype'] = xarMLS::DNTYPE_PROPERTY;
                $args['dnname'] = $dnname1;
                $args['subtype'] = $ctxtype2;
                $args['subname'] = $ctxname1;
                $entry = xarMod::apiFunc('translations', 'admin', 'getcontextentries', $args);
                if ($entry['numEntries']+$entry['numKeyEntries'] > 0) {
                    $entrydata[] = $entry;
                    $subtypes[] = $ctxtype2;
                    $subnames[] = $ctxname1;
                }
            }
            break;

        case xarMLS::DNTYPE_BLOCK:

            $blockinfo = xarMod::apiFunc('blocks', 'types', 'getitem', ['type_id' => $extid, 'type_state' => xarBlock::TYPE_STATE_ACTIVE]);

            $blockname = $blockinfo['type'];
            $blockdir = $blockinfo['type'];

            $selectedsubtype = $subtype;
            $selectedsubname = $subname;

            $block_contexts_list[] = 'blocks:'.$blockname.'::common';

            $subnames = xarMod::apiFunc('translations', 'admin', 'get_block_phpfiles', ['blockdir'=>$blockdir]);
            foreach ($subnames as $subname) {
                $block_contexts_list[] = 'blocks:'.$blockname.'::'.$subname;
            }

            $dirnames = xarMod::apiFunc('translations', 'admin', 'get_block_dirs', ['blockdir'=>$blockdir]);
            foreach ($dirnames as $dirname) {
                if (!preg_match('!^templates!i', $dirname, $matches)) {
                    $pattern = '/^([a-z0-9\-_]+)\.php$/i';
                } else {
                    $pattern = '/^([a-z0-9\-_]+)\.xt$/i';
                }
                $subnames = xarMod::apiFunc(
                    'translations',
                    'admin',
                    'get_block_files',
                    ['blockdir'=>sys::code() . "blocks/$blockdir/xar$dirname",'pattern'=>$pattern]
                );
                foreach ($subnames as $subname) {
                    $block_contexts_list[] = 'blocks:'.$blockname.':'.$dirname.':'.$subname;
                }
            }

            $subtypes = [];
            $subnames = [];
            $entrydata = [];
            foreach ($block_contexts_list as $block_context) {
                [$dntype1, $dnname1, $ctxtype1, $ctxname1] = explode(':', $block_context);
                $args = [];
                $ctxtype2 = 'blocks:'.$ctxtype1;
                $args['dntype'] = xarMLS::DNTYPE_BLOCK;
                $args['dnname'] = $dnname1;
                $args['subtype'] = $ctxtype2;
                $args['subname'] = $ctxname1;
                $entry = xarMod::apiFunc('translations', 'admin', 'getcontextentries', $args);
                if ($entry['numEntries']+$entry['numKeyEntries'] > 0) {
                    $entrydata[] = $entry;
                    $subtypes[] = $ctxtype2;
                    $subnames[] = $ctxname1;
                }
            }
            break;

        case xarMLS::DNTYPE_THEME:
            $themeid = $extid;
            if (!$themeinfo = xarMod::getInfo($themeid, 'theme')) {
                return;
            }

            $themename = $themeinfo['osdirectory'];
            $themedir = $themeinfo['osdirectory'];

            $selectedsubtype = $subtype;
            $selectedsubname = $subname;

            $theme_contexts_list[] = [
                                        'dntype' => xarMLS::DNTYPE_THEME,
                                        'dnname' => $themename,
                                        'subtype' => 'themes:',
                                        'subname' => 'common', ];

            $files = xarMod::apiFunc('translations', 'admin', 'get_files', ['themedir'=>$themedir]);

            $prefix = 'themes/'.$themename;
            foreach ($files as $file) {
                $dirname = dirname($file);
                if (strpos($prefix, $dirname) == 0) {
                    $dirname = substr($dirname, strlen($prefix) + 1);
                } else {
                    throw new Exception('mismatch: ' . $prefix . " " . $dirname);
                }
                $subname = basename($file, '.xt');
                $theme_contexts_list[] = [
                                            'dntype' => xarMLS::DNTYPE_THEME,
                                            'dnname' => $themename,
                                            'subtype' => 'themes:' . $dirname,
                                            'subname' => $subname, ];
            }

            $subtypes = [];
            $subnames = [];
            $entrydata = [];
            foreach ($theme_contexts_list as $theme_context) {
                $entry = xarMod::apiFunc('translations', 'admin', 'getcontextentries', $theme_context);
                if ($entry['numEntries']+$entry['numKeyEntries'] > 0) {
                    $entrydata[] = $entry;
                    $subtypes[] = $theme_context['subtype'];
                    $subnames[] = $theme_context['subname'];
                }
            }
            break;

        case xarMLS::DNTYPE_OBJECT:
            $objectid = $extid;
            sys::import('modules.dynamicdata.class.objects.master');
            $object = DataObjectMaster::getObject(['objectid' => $objectid]);

            if (!$objectinfo = DataObjectMaster::getObjectInfo(['objectid' => $objectid])) {
                return;
            }

            $objectname = $object->name;
            $objectdir = $object->name;

            $selectedsubtype = $subtype;
            $selectedsubname = $subname;

            $object_contexts_list[] = [
                                        'dntype'  => xarMLS::DNTYPE_OBJECT,
                                        'dnname'  => 'object',
                                        'subtype' => 'objects:',
                                        'subname' => 'common', ];

            $propertynames = xarMod::apiFunc('translations', 'admin', 'get_object_properties', ['object' => $object]);

            $prefix = 'objects/'.$objectname;
            foreach ($propertynames as $name) {
                $subname = $name;
                $object_contexts_list[] = [
                                            'dntype'  => xarMLS::DNTYPE_OBJECT,
                                            'dnname'  => $objectname,
                                            'subtype' => 'objects:'.$objectname,
                                            'subname' => $subname, ];
            }

            $subtypes = [];
            $subnames = [];
            $entrydata = [];
            foreach ($object_contexts_list as $object_context) {
                $entry = xarMod::apiFunc('translations', 'admin', 'getcontextentries', $object_context);
                if ($entry['numEntries']+$entry['numKeyEntries'] > 0) {
                    $entrydata[] = $entry;
                    $subtypes[] = $object_context['subtype'];
                    $subnames[] = $object_context['subname'];
                }
            }
            break;
    }
    $subData = ['subtypes'=>$subtypes,
                 'subnames'=>$subnames,
                 'entrydata'=>$entrydata,
                 'selectedsubtype'=>$selectedsubtype,
                 'selectedsubname'=>$selectedsubname, ];
    return $subData;
}

function translations_working_locale($locale = null)
{
    if (!$locale) {
        $locale = xarSession::getVar('translations_working_locale');
        if (!$locale) {
            $locale = xarMLS::getCurrentLocale();
            xarSession::setVar('translations_working_locale', $locale);
        }
        return $locale;
    } else {
        xarSession::setVar('translations_working_locale', $locale);
    }
}

function translations_release_locale($locale = null)
{
    if (!$locale) {
        $locale = xarSession::getVar('translations_release_locale');
        if (!$locale) {
            $locale = translations_working_locale();
            xarSession::setVar('translations_release_locale', $locale);
        }
        return $locale;
    } else {
        xarSession::setVar('translations_release_locale', $locale);
    }
}
