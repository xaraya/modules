<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Marco Canini
// Purpose of file: translations admin GUI
// ----------------------------------------------------------------------

define('CHOOSE', -1);
define('INFO', 0);
define('GEN', 1);
define('REL', 2);
define('DOWNLOAD', 3);

define('OVERVIEW', 0);
define('GEN_SKELS', 1);
define('TRANSLATE', 2);
define('GEN_TRANS', 3);
define('RELEASE', 4);

/* EVENT */function translations_adminevt_OnModLoad($args)
{
    if (xarMLSGetMode() != XARMLS_UNBOXED_MULTI_LANGUAGE_MODE) {
        $msg = xarML('To execute the translations module you must set the Multi Language System mode to UNBOXED.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'WrongMLSMode', new DefaultUserException($msg));
        return;
    }
    xarTplSetPageTitle(xarML('Welcome to translators\' paradise!'));

}

/* FUNC */function translations_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $tplData['locales'] = xarLocaleGetList(array('charset'=>'utf-8'));
    $tplData['working_locale'] = translations_working_locale();

    return $tplData;
}

/* FUNC */function translations_admin_update_working_locale()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('locale', 'str:1:', $locale)) return;
    translations_working_locale($locale);
    xarResponseRedirect(xarModURL('translations', 'admin'));
}

/* FUNC */function translations_admin_update_release_locale()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('locale', 'str:1:', $locale)) return;
    translations_release_locale($locale);
    xarResponseRedirect(xarModURL('translations', 'admin', 'generate_trans_info'));
}

/* FUNC */function translations_admin_update_info()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('ctxtype', 'regexp:/^(core|module|theme)$/', $type)) return;

    switch ($type) {
        case 'core':
        $url = xarModURL('translations', 'admin', 'core_overview');
        xarSessionSetVar('translations_dnType', XARMLS_DNTYPE_CORE);
        break;
        case 'module':
        $url = xarModURL('translations', 'admin', 'choose_a_module');
        xarSessionSetVar('translations_dnType', XARMLS_DNTYPE_MODULE);
        break;
        case 'theme':
        $url = xarModURL('translations', 'admin', 'choose_a_theme');
        xarSessionSetVar('translations_dnType', XARMLS_DNTYPE_THEME);
        break;
    }
    xarResponseRedirect($url);
}

/* FUNC */function translations_admin_core_overview()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    xarSessionSetVar('translations_dnName', 'xaraya');

    $tplData = translations_create_opbar(OVERVIEW);
    $tplData['verNum'] = XARCORE_VERSION_NUM;
    $tplData['verId'] = XARCORE_VERSION_ID;
    $tplData['verSub'] = XARCORE_VERSION_SUB;
    return $tplData;
}

/* FUNC */function translations_admin_choose_a_module()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!($modlist = xarModGetList())) return;

    $tplData = translations_create_choose_a_module_druidbar(CHOOSE);
    $tplData['modlist'] = $modlist;
    return $tplData;
}

/* FUNC */function translations_admin_choose_a_theme()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!($themelist = xarThemeGetList())) return;

    $tplData = translations_create_choose_a_theme_druidbar(CHOOSE);
    $tplData['themelist'] = $themelist;
    return $tplData;
}

/* FUNC */function translations_admin_module_overview()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $sessmodid = xarSessionGetVar('translations_modid');
    if (!xarVarFetch('modid', 'id', $modid, $sessmodid)) return;
    xarSessionSetVar('translations_modid', $modid);

    if (!($tplData = xarModGetInfo($modid))) return;

    xarSessionSetVar('translations_dnName', $tplData['name']);

    $druidbar = translations_create_choose_a_module_druidbar(OVERVIEW);
    $opbar = translations_create_opbar(OVERVIEW);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

/* FUNC */function translations_admin_theme_overview()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $sessmodid = xarSessionGetVar('translations_modid');
    if (!xarVarFetch('modid', 'id', $modid, $sessmodid)) return;
    xarSessionSetVar('translations_modid', $modid);

    if (!($tplData = xarModGetInfo($modid))) return;

    xarSessionSetVar('translations_dnName', $tplData['name']);

    $druidbar = translations_create_choose_a_theme_druidbar(OVERVIEW);
    $opbar = translations_create_opbar(OVERVIEW);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

/* FUNC */function translations_admin_generate_skels_info()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $druidbar = translations_create_generate_skels_druidbar(INFO);
    $opbar = translations_create_opbar(GEN_SKELS);
    $tplData = array_merge($druidbar, $opbar);

    return $tplData;
}

/* FUNC */function translations_admin_generate_skels()
{

    $dnType = xarSessionGetVar('translations_dnType');
    $locale = translations_working_locale();
    $args = array('locale'=>$locale);
    switch ($dnType) {
        case XARMLS_DNTYPE_CORE:
        $res = xarModAPIFunc('translations','admin','generate_core_skels',$args);
        break;
        case XARMLS_DNTYPE_MODULE:
        $args['modid'] = xarSessionGetVar('translations_modid');
        $res = xarModAPIFunc('translations','admin','generate_module_skels',$args);
        break;
    }
    if (!isset($res)) return;

    xarSessionSetVar('translations_result', $res);
    xarResponseRedirect(xarModURL('translations', 'admin', 'generate_skels_result'));
}

/* FUNC */function translations_admin_generate_skels_result()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $tplData = xarSessionGetVar('translations_result');
    if ($tplData == NULL) {
        xarResponseRedirect(xarModURL('translations', 'admin', 'generate_skels_info'));
    }
    xarSessionDelVar('translations_result');

    $druidbar = translations_create_generate_skels_druidbar(GEN);
    $opbar = translations_create_opbar(GEN_SKELS);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

/* FUNC */function translations_admin_generate_trans_info()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $locale = translations_release_locale();
    $l = xarLocaleGetInfo($locale);
    $tplData['locales'] = xarLocaleGetList(array('lang' => $l['lang']));
    $tplData['release_locale'] = $locale;
    $tplData['archiver_path'] = xarModAPIFunc('translations','admin','archiver_path');

    $druidbar = translations_create_generate_trans_druidbar(INFO);
    $opbar = translations_create_opbar(GEN_TRANS);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

/* FUNC */function translations_admin_generate_trans()
{
    $dnType = xarSessionGetVar('translations_dnType');
    $locale = translations_release_locale();
    $args = array('locale'=>$locale);
    switch ($dnType) {
        case XARMLS_DNTYPE_CORE:
        $res = xarModAPIFunc('translations','admin','generate_core_trans',$args);
        break;
        case XARMLS_DNTYPE_MODULE:
        $args['modid'] = xarSessionGetVar('translations_modid');
        $res = xarModAPIFunc('translations','admin','generate_module_trans',$args);
        break;
    }
    if (!isset($res)) return;

    xarSessionSetVar('translations_result', $res);
    xarResponseRedirect(xarModURL('translations', 'admin', 'generate_trans_result'));
}

/* FUNC */function translations_admin_generate_trans_result()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $tplData = xarSessionGetVar('translations_result');
    if ($tplData == NULL) {
        xarResponseRedirect(xarModURL('translations', 'admin', 'generate_trans_info'));
    }
    xarSessionDelVar('translations_result');

    $druidbar = translations_create_generate_trans_druidbar(GEN);
    $opbar = translations_create_opbar(GEN_TRANS);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

/* FUNC */function translations_admin_release()
{
    $dnType = xarSessionGetVar('translations_dnType');
    $locale = translations_release_locale();
    $args = array('locale'=>$locale);
    switch ($dnType) {
        case XARMLS_DNTYPE_CORE:
        $res = xarModAPIFunc('translations','admin','release_core_trans',$args);
        break;
        case XARMLS_DNTYPE_MODULE:
        $args['modid'] = xarSessionGetVar('translations_modid');
        $res = xarModAPIFunc('translations','admin','release_module_trans',$args);
        break;
    }
    if (!isset($res)) return;

    xarSessionSetVar('translations_filename', $res);
    xarResponseRedirect(xarModURL('translations', 'admin', 'release_result'));
}

/* FUNC */function translations_admin_release_result()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $filename = xarSessionGetVar('translations_filename');
    if ($filename == NULL) {
        xarResponseRedirect(xarModURL('translations', 'admin', 'release_info'));
    }
    xarSessionDelVar('translations_filename');

    $tplData['url'] = xarServerGetBaseURL().xarCoreGetVarDirPath().'/cache/'.$filename;

    $druidbar = translations_create_generate_trans_druidbar(REL);
    $opbar = translations_create_opbar(GEN_TRANS);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

/* FUNC */function translations_admin_translate()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $opbar = translations_create_opbar(TRANSLATE);
    $trabar = translations_create_trabar('', '');

    $tplData = array_merge($opbar, $trabar);

    return $tplData;
}

/* FUNC */function translations_admin_translate_subtype()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('subtype', 'regexp:/^(file|template|incltempl|blktempl|block)$/', $subtype)) return;
    if (!xarVarFetch('subname', 'str:1:', $subname)) return;

    $dnType = xarSessionGetVar('translations_dnType');
    $dnName = xarSessionGetVar('translations_dnName');

    if ($subtype == 'file') $ctxType = XARMLS_CTXTYPE_FILE;
    elseif ($subtype == 'template') $ctxType = XARMLS_CTXTYPE_TEMPLATE;
    elseif ($subtype == 'incltempl') $ctxType = XARMLS_CTXTYPE_INCLTEMPL;
	elseif ($subtype == 'blktempl') $ctxType = XARMLS_CTXTYPE_BLKTEMPL;
    else $ctxType = XARMLS_CTXTYPE_BLOCK;
    $ctxName = $subname;

    $locale = translations_working_locale();

    $args['interface'] = 'ReferencesBackend';
    $args['locale'] = $locale;
    $backend = xarModAPIFunc('translations','admin','create_backend_instance',$args);

    if (!isset($backend)) return;
    if (!$backend->bindDomain($dnType, $dnName)) {
        if ($dnType == XARMLS_DNTYPE_MODULE) {
            $msg = xarML('Could not bind translation backend to module \'#(1)\'. Try regenerating skeletons.', $dnName);
        } elseif ($dnType == XARMLS_DNTYPE_THEME) {
            $msg = xarML('Could not bind translation backend to theme \'#(1)\'. Try regenerating skeletons.', $dnName);
        } elseif ($dnType == XARMLS_DNTYPE_CORE) {
            $msg = xarML('Could not bind translation backend to core. Try regenerating skeletons.');
        } else {
            $msg = xarML('Could not bind translation: unknown domain type');
        }
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN', $msg);
        return;
    }
    if (!$backend->loadContext($ctxType, $ctxName)) return;

    if ($locale != 'en_US.utf-8') {
        // Load an english backend for original key translations
        $args['interface'] = 'ReferencesBackend';
        $args['locale'] = 'en_US.utf-8';
        $en_backend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
        if (!isset($en_backend)) return;
        if ($en_backend->bindDomain($dnType, $dnName) &&
            !$en_backend->loadContext($ctxType, $ctxName)) return;
    } else {
        $en_backend =& $backend;
    }

    $maxReferences = 5;

    $numEntries = 0;
    $entries = array();
    while (list($string, $translation) = $backend->enumTranslations()) {
        $entry = array('string' => htmlspecialchars($string), 'translation' => htmlspecialchars($translation), 'tid' => $backend->getTransientId($string));
        $e = $backend->getEntry($string);
        $entry['references'] = translations_grab_source_code($e['references'], $maxReferences);
        if (count($e['references']) > $maxReferences) {
            $entry['otherReferences'] = true;
            $entry['numOtherReferences'] = count($e['references']) - $maxReferences;
        } else {
            $entry['otherReferences'] = false;
        }
        $entries[] = $entry;
        $numEntries++;
    }

    $numKeyEntries = 0;
    $keyEntries = array();
    while (list($key, $translation) = $backend->enumKeyTranslations()) {
        $keyEntry = array('key' => htmlspecialchars($key), 'translation' => htmlspecialchars($translation));
        $e = $backend->getEntryByKey($key);
        $keyEntry['references'] = translations_grab_source_code($e['references'], $maxReferences);
        if (count($e['references']) > $maxReferences) {
            $keyEntry['otherReferences'] = true;
            $keyEntry['numOtherReferences'] = count($e['references']) - $maxReferences;
        } else {
            $keyEntry['otherReferences'] = false;
        }
        $en_translation = $en_backend->translateByKey($key);
        if (!$en_translation) {
            $en_translation = xarML('(Original translation not found)');
        }
        $keyEntry['en_translation'] = $en_translation;
        $keyEntries[] = $keyEntry;
        $numKeyEntries++;
    }

    $action = xarModURL('translations', 'admin', 'translate_update', array('subtype'=>$subtype, 'subname'=>$subname, 'numEntries'=>$numEntries, 'numKeyEntries'=>$numKeyEntries));
    $tplData = array('entries'=>$entries, 'keyEntries'=>$keyEntries, 'action'=>$action);

    $opbar = translations_create_opbar(TRANSLATE);
    $trabar = translations_create_trabar($subtype, $subname);

    $tplData = array_merge($tplData, $opbar, $trabar);

    xarTplAddStyleLink('translations', 'translate_subtype');
    return $tplData;
}

function translations_admin_translate_update()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('subtype', 'regexp:/^(file|template|incltempl|blktempl|block)$/', $subtype)) return;
    if (!xarVarFetch('subname', 'str:1:', $subname)) return;
    if (!xarVarFetch('numEntries', 'int:0:', $numEntries)) return;
    if (!xarVarFetch('numKeyEntries', 'int:0:', $numKeyEntries)) return;

    $dnType = xarSessionGetVar('translations_dnType');
    $dnName = xarSessionGetVar('translations_dnName');

    if ($subtype == 'file') $ctxType = XARMLS_CTXTYPE_FILE;
    elseif ($subtype == 'template') $ctxType = XARMLS_CTXTYPE_TEMPLATE;
    elseif ($subtype == 'incltempl') $ctxType = XARMLS_CTXTYPE_INCLTEMPL;
    elseif ($subtype == 'blktempl') $ctxType = XARMLS_CTXTYPE_BLKTEMPL;
    else $ctxType = XARMLS_CTXTYPE_BLOCK;
    $ctxName = $subname;

    $locale = translations_working_locale();

    $args['interface'] = 'ReferencesBackend';
    $args['locale'] = $locale;
    $backend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
    if (!isset($backend)) return;
    if (!$backend->bindDomain($dnType, $dnName)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        return;
    }
    if (!$backend->loadContext($ctxType, $ctxName)) return;

    $args['interface'] = 'ReferencesGenerator';
    $args['locale'] = $locale;
    $gen = xarModAPIFunc('translations','admin','create_generator_instance',$args);
    if (!isset($gen)) return;
    if (!$gen->bindDomain($dnType, $dnName)) return;
    if (!$gen->create($ctxType, $ctxName)) return;

    for ($i = 0; $i < $numEntries; $i++) {
        unset($translation);
        if (!xarVarFetch('tid'.$i, 'str::', $translation, '', XARVAR_POST_ONLY)) return;
        // Lookup the string bounded to the tid$i transient id
        $e = $backend->lookupTransientId($i);
        if ($e) {
            $gen->addEntry($e['string'], $e['references'], $translation);
        }
    }
    while (list($key, $translation) = $backend->enumKeyTranslations()) {
        unset($translation);
        if (!xarVarFetch('key'.$key, 'str::', $translation, '', XARVAR_POST_ONLY)) return;
        $e = $backend->getEntryByKey($key);
        if ($e) {
            $gen->addKeyEntry($key, $e['references'], $translation);
        }
    }

    $gen->close();

    xarResponseRedirect(xarModURL('translations', 'admin', 'translate_subtype', array('subtype'=>$subtype, 'subname'=>$subname)));
}

/* FUNC */function translations_admin_translate_templates()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $dnType = xarSessionGetVar('translations_dnType');
    $dnName = xarSessionGetVar('translations_dnName');

    $locale = translations_working_locale();

    $args['interface'] = 'ReferencesBackend';
    $args['locale'] = $locale;
    $backend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
    if (!isset($backend)) return;
    if (!$backend->bindDomain($dnType, $dnName)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        return;
    }
    $tplData['subnames'] = $backend->getContextNames(XARMLS_CTXTYPE_TEMPLATE);

    $opbar = translations_create_opbar(TRANSLATE);
    $trabar = translations_create_trabar('template', '');

    $tplData = array_merge($tplData, $opbar, $trabar);

    return $tplData;
}

/* FUNC */function translations_admin_translate_incltempl()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $dnType = xarSessionGetVar('translations_dnType');
    $dnName = xarSessionGetVar('translations_dnName');

    $locale = translations_working_locale();

    $args['interface'] = 'ReferencesBackend';
    $args['locale'] = $locale;
    $backend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
    if (!isset($backend)) return;
    if (!$backend->bindDomain($dnType, $dnName)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        return;
    }
    $tplData['subnames'] = $backend->getContextNames(XARMLS_CTXTYPE_INCLTEMPL);

    $opbar = translations_create_opbar(TRANSLATE);
    $trabar = translations_create_trabar('incltempl', '');

    $tplData = array_merge($tplData, $opbar, $trabar);

    return $tplData;
}

/* FUNC */function translations_admin_translate_blktempl()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $dnType = xarSessionGetVar('translations_dnType');
    $dnName = xarSessionGetVar('translations_dnName');

    $locale = translations_working_locale();

    $args['interface'] = 'ReferencesBackend';
    $args['locale'] = $locale;
    $backend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
    if (!isset($backend)) return;
    if (!$backend->bindDomain($dnType, $dnName)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        return;
    }
    $tplData['subnames'] = $backend->getContextNames(XARMLS_CTXTYPE_BLKTEMPL);

    $opbar = translations_create_opbar(TRANSLATE);
    $trabar = translations_create_trabar('blktempl', '');

    $tplData = array_merge($tplData, $opbar, $trabar);

    return $tplData;
}

/* FUNC */function translations_admin_translate_blocks()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $dnType = xarSessionGetVar('translations_dnType');
    $dnName = xarSessionGetVar('translations_dnName');

    $locale = translations_working_locale();

    $args['interface'] = 'ReferencesBackend';
    $args['locale'] = $locale;
    $backend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
    if (!isset($backend)) return;
    if (!$backend->bindDomain($dnType, $dnName)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        return;
    }
    $tplData['subnames'] = $backend->getContextNames(XARMLS_CTXTYPE_BLOCK);

    $opbar = translations_create_opbar(TRANSLATE);
    $trabar = translations_create_trabar('block', '');

    $tplData = array_merge($tplData, $opbar, $trabar);

    return $tplData;
}

/* FUNC */function translations_admin_test()
{
    $args = array('testcomponent' => 'Pippo::', 'testinstance' => '.*');
    $res = xarModAPIFunc('permissions', 'admin', 'query_access_level', $args);
    ob_start();
    var_dump($res);
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
}

// PRIVATE STUFF

function translations_grab_source_code($references, $maxReferences = NULL)
{
    static $files = array();
    $result = array();
    if ($maxReferences == NULL) {
        $maxReferences = count($references);
    }
    for ($i = 0; $i < count($references) && $i < $maxReferences; $i++) {
        $ref = $references[$i];
        if (!isset($files[$ref['file']])) {
            $files[$ref['file']] = file($ref['file']);
        }
        $j = $ref['line'] - 3;
        if ($j < 0) $j = 0;
        $source = array('pre'=>'', 'code'=>'', 'post'=>'');
        for ($c = 0; $c < 5 && $j < count($files[$ref['file']]); $c++, $j++) {
            if ($j < $ref['line'] - 1) {
                $source['pre'] .= htmlspecialchars($files[$ref['file']][$j]).'<br/>';
            } elseif ($j == $ref['line'] - 1) {
                $source['code'] = htmlspecialchars($files[$ref['file']][$j]).'<br/>';
            } else {
                $source['post'] .= htmlspecialchars($files[$ref['file']][$j]).'<br/>';
            }
        }
        $ref['source'] = $source;
        $result[] = $ref;
    }
    return $result;
}

function translations_create_generate_skels_druidbar($currentStep) {
    $stepLabels[INFO] = xarML('Informations');
    $stepLabels[GEN] = xarML('Generation');
    $stepURLs[INFO] = xarModURL('translations', 'admin', 'generate_skels_info');
    $stepURLs[GEN] = NULL;

    return array('stepLabels'=>$stepLabels, 'stepURLs'=>$stepURLs, 'currentStep'=>$currentStep);
}

function translations_create_generate_trans_druidbar($currentStep) {
    $stepLabels[INFO] = xarML('Informations');
    $stepLabels[GEN] = xarML('Generation');
    $stepLabels[REL] = xarML('Release');
    $stepLabels[DOWNLOAD] = xarML('Download');
    $stepURLs[INFO] = xarModURL('translations', 'admin', 'generate_trans_info');
    $stepURLs[GEN] = xarModURL('translations', 'admin', 'generate_trans');
    $stepURLs[REL] = xarModURL('translations', 'admin', 'generate_release');
    $stepURLs[DOWNLOAD] = NULL;

    return array('stepLabels'=>$stepLabels, 'stepURLs'=>$stepURLs, 'currentStep'=>$currentStep);
}

function translations_create_choose_a_module_druidbar($currentStep) {
    // This + 1 is actually an "hack"
    $stepLabels[CHOOSE + 1] = xarML('Choose a module');
    $stepLabels[OVERVIEW + 1] = xarML('Overview');
    $stepURLs[CHOOSE + 1] = xarModURL('translations', 'admin', 'choose_a_module');
    $stepURLs[OVERVIEW + 1] = NULL;

    return array('stepLabels'=>$stepLabels, 'stepURLs'=>$stepURLs, 'currentStep'=>$currentStep + 1);
}

function translations_create_choose_a_theme_druidbar($currentStep) {
    // This + 1 is actually an "hack"
    $stepLabels[CHOOSE + 1] = xarML('Choose a theme');
    $stepLabels[OVERVIEW + 1] = xarML('Overview');
    $stepURLs[CHOOSE + 1] = xarModURL('translations', 'admin', 'choose_a_theme');
    $stepURLs[OVERVIEW + 1] = NULL;

    return array('stepLabels'=>$stepLabels, 'stepURLs'=>$stepURLs, 'currentStep'=>$currentStep + 1);
}

function translations_create_opbar($currentOp)
{
    $dnType = xarSessionGetVar('translations_dnType');
    $dnName = xarSessionGetVar('translations_dnName');

    // Overview | Generate skels | Translate | Generate translations | Release translations package
    $opLabels[OVERVIEW] = xarML('Overview');
    $opLabels[GEN_SKELS] = xarML('Generate skels');
    $opLabels[TRANSLATE] = xarML('Translate');
    $opLabels[GEN_TRANS] = xarML('Generate translations');
    //$opLabels[RELEASE] = xarML('Release translations package');

    switch ($dnType) {
        case XARMLS_DNTYPE_CORE:
        $opURLs[OVERVIEW] = xarModURL('translations', 'admin', 'core_overview');
        break;
        case XARMLS_DNTYPE_MODULE:
        $opURLs[OVERVIEW] = xarModURL('translations', 'admin', 'module_overview');
        break;
        case XARMLS_DNTYPE_THEME:
        $opURLs[OVERVIEW] = xarModURL('translations', 'admin', 'theme_overview');
        break;
    }
    $opURLs[GEN_SKELS] = xarModURL('translations', 'admin', 'generate_skels_info');
    $opURLs[TRANSLATE] = xarModURL('translations', 'admin', 'translate');
    $opURLs[GEN_TRANS] = xarModURL('translations', 'admin', 'generate_trans_info');
    //$opURLs[RELEASE] = xarModURL('translations', 'admin', 'release_info');

    $enabledOps = array(true, true, false, false/*, false*/); // Enables See module details & Generate translations skels

    $locale = translations_working_locale();
    $args['interface'] = 'ReferencesBackend';
    $args['locale'] = $locale;
    $backend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
    if (!isset($backend)) return;
    if ($backend->bindDomain($dnType, $dnName)) {
        $enabledOps[TRANSLATE] = true; // Enables Translate
        $enabledOps[GEN_TRANS] = true; // Enables Generate translations
        /*$args['interface'] = 'TranslationsBackend';
        $args['locale'] = $locale;
        $backend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
        if (!isset($backend)) return;
        if ($backend->bindDomain($dnType, $dnName)) {
            $enabledOps[RELEASE] = true; // Enables Release translations package
        }*/
    }

    return array('opLabels'=>$opLabels, 'opURLs'=>$opURLs, 'enabledOps'=>$enabledOps, 'currentOp'=>$currentOp);
}

function translations_create_trabar($subtype, $subname)
{
    $dnType = xarSessionGetVar('translations_dnType');
    $dnName = xarSessionGetVar('translations_dnName');

    $currentTra = -1;
    switch ($dnType) {
        case XARMLS_DNTYPE_CORE:
        $traLabels[0] = 'core';

        $traURLs[0] = xarModURL('translations', 'admin', 'translate_subtype', array('subtype'=>'file', 'subname'=>'core'));

        $enabledTras = array(true);

        if ($subtype == 'file') {
            $currentTra = 0;
        }
        break;
        case XARMLS_DNTYPE_MODULE:

        $subnames = xarModAPIFunc('translations','admin','get_module_phpfiles',
                                  array('moddir'=>$dnName));
        $j = 0;
        foreach ($subnames as $subnameinlist) {
            $traLabels[$j] = $subnameinlist;
            $traURLs[$j] = xarModURL('translations', 'admin', 'translate_subtype', array('subtype'=>'file', 'subname'=>$subnameinlist));
            $enabledTras[$j] = true;
            $j++;
        }
        $traLabels[$j] = 'common';
        $traURLs[$j] = xarModURL('translations', 'admin', 'translate_subtype', array('subtype'=>'file', 'subname'=>'common'));
        $enabledTras[$j++] = true;
        $traLabels[$j] = 'templates';
        $traURLs[$j] = xarModURL('translations', 'admin', 'translate_templates');
        $enabledTras[$j++] = true;
        $traLabels[$j] = 'incltempl';
        $traURLs[$j] = xarModURL('translations', 'admin', 'translate_incltempl');
        $enabledTras[$j++] = true;
        $traLabels[$j] = 'blktempl';
        $traURLs[$j] = xarModURL('translations', 'admin', 'translate_blktempl');
        $enabledTras[$j++] = true;
        $traLabels[$j] = 'blocks';
        $traURLs[$j] = xarModURL('translations', 'admin', 'translate_blocks');
        $enabledTras[$j++] = true;

        // $enabledTras = array(true, true, true, true, true, true, true, true);

        switch ($subtype) {
            case 'file':
            $currentTra = array_search($subname, $traLabels);
            break;
            case 'template':
            if ($subname == '') {
                $currentTra = array_search('templates', $traLabels);
            }
            break;
            case 'incltempl':
            if ($subname == '') {
                $currentTra = array_search('incltempl', $traLabels);
            }
            break;
            case 'block':
            if ($subname == '') {
                $currentTra = array_search('blocks', $traLabels);
            }
            break;
        }
        break;
        case XARMLS_DNTYPE_THEME:
        // TODO
        break;
    }

    return array('traLabels'=>$traLabels, 'traURLs'=>$traURLs, 'enabledTras'=>$enabledTras, 'currentTra'=>$currentTra);
}

function translations_working_locale($locale = NULL)
{
    if (!$locale) {
        $locale = xarSessionGetVar('translations_working_locale');
        if (!$locale) {
            $locale = xarMLSGetCurrentLocale();
            xarSessionSetVar('translations_working_locale', $locale);
        }
        return $locale;
    } else {
        xarSessionSetVar('translations_working_locale', $locale);
    }
}

function translations_release_locale($locale = NULL)
{
    if (!$locale) {
        $locale = xarSessionGetVar('translations_release_locale');
        if (!$locale) {
            $locale = translations_working_locale();
            xarSessionSetVar('translations_release_locale', $locale);
        }
        return $locale;
    } else {
        xarSessionSetVar('translations_release_locale', $locale);
    }
}
?>