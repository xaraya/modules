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

define('CHOOSE', 0);
define('INFO', 1);
define('GEN', 2);
define('TRAN',3);
define('REL', 3);
define('DOWNLOAD', 4);

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

// PRIVATE STUFF

function translations_create_choose_a_module_druidbar($currentStep) 
{
    $stepLabels[CHOOSE] = xarML('Choose a module');
    $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'choose_a_module');

    return array('stepLabels'=>$stepLabels, 'stepURLs'=>$stepURLs, 'currentStep'=>$currentStep);
}

function translations_create_choose_a_theme_druidbar($currentStep) 
{
    $stepLabels[CHOOSE] = xarML('Choose a theme');
    $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'choose_a_theme');

    return array('stepLabels'=>$stepLabels, 'stepURLs'=>$stepURLs, 'currentStep'=>$currentStep);
}

function translations_create_module_overview_druidbar($currentStep) 
{
    $stepLabels[CHOOSE] = xarML('Choose a module');
    $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'choose_a_module');
    $stepLabels[INFO] = xarML('Overview');
    $stepURLs[INFO] = NULL;

    return array('stepLabels'=>$stepLabels, 'stepURLs'=>$stepURLs, 'currentStep'=>$currentStep);
}

function translations_create_theme_overview_druidbar($currentStep) 
{
    $stepLabels[CHOOSE] = xarML('Choose a theme');
    $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'choose_a_theme');
    $stepLabels[INFO] = xarML('Overview');
    $stepURLs[INFO] = NULL;

    return array('stepLabels'=>$stepLabels, 'stepURLs'=>$stepURLs, 'currentStep'=>$currentStep);
}

function translations_create_generate_skels_druidbar($currentStep, $tran_type) 
{
    switch($tran_type) {
    case XARMLS_DNTYPE_CORE:
        $stepLabels[CHOOSE] = xarML('Core');
        $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'core_overview');
        $stepLabels[INFO] = xarML('Overview');
        $stepURLs[INFO] = xarModURL('translations', 'admin', 'core_overview');
        break;
    case XARMLS_DNTYPE_MODULE:
        $stepLabels[CHOOSE] = xarML('Choose a module');
        $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'choose_a_module');
        $stepLabels[INFO] = xarML('Overview');
        $stepURLs[INFO] = xarModURL('translations', 'admin', 'module_overview');
        break;
    case XARMLS_DNTYPE_THEME:
        $stepLabels[CHOOSE] = xarML('Choose a theme');
        $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'choose_a_theme');
        $stepLabels[INFO] = xarML('Overview');
        $stepURLs[INFO] = xarModURL('translations', 'admin', 'theme_overview');
        break;
    }
    $stepLabels[GEN] = xarML('Generation');
    $stepURLs[GEN] = NULL;

    return array('stepLabels'=>$stepLabels, 'stepURLs'=>$stepURLs, 'currentStep'=>$currentStep);
}

function translations_create_translate_druidbar($currentStep, $tran_type) 
{
    switch($tran_type) {
    case XARMLS_DNTYPE_CORE:
        $stepLabels[CHOOSE] = xarML('Core');
        $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'core_overview');
        $stepLabels[INFO] = xarML('Overview');
        $stepURLs[INFO] = xarModURL('translations', 'admin', 'core_overview');
        break;
    case XARMLS_DNTYPE_MODULE:
        $stepLabels[CHOOSE] = xarML('Choose a module');
        $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'choose_a_module');
        $stepLabels[INFO] = xarML('Overview');
        $stepURLs[INFO] = xarModURL('translations', 'admin', 'module_overview');
        break;
    case XARMLS_DNTYPE_THEME:
        $stepLabels[CHOOSE] = xarML('Choose a theme');
        $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'choose_a_theme');
        $stepLabels[INFO] = xarML('Overview');
        $stepURLs[INFO] = xarModURL('translations', 'admin', 'theme_overview');
        break;
    }
    $stepLabels[GEN] = xarML('Generation');
    $stepURLs[GEN] = xarModURL('translations','admin','generate_skels_info');

    $stepLabels[TRAN] = xarML('Translate');
    $stepURLs[TRAN] = NULL;

    return array('stepLabels'=>$stepLabels, 'stepURLs'=>$stepURLs, 'currentStep'=>$currentStep);
}

function translations_create_generate_trans_druidbar($currentStep, $tran_type) 
{
    $sessmodid = xarSessionGetVar('translations_modid');
    if (!xarVarFetch('modid', 'id', $modid, $sessmodid)) return;

    switch($tran_type) {
    case XARMLS_DNTYPE_CORE:
        $stepLabels[CHOOSE] = xarML('Core');
        $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'core_overview');
        break;
    case XARMLS_DNTYPE_MODULE:
        $stepLabels[CHOOSE] = xarML('Choose a module');
        $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'choose_a_module');
        break;
    case XARMLS_DNTYPE_THEME:
        $stepLabels[CHOOSE] = xarML('Choose a theme');
        $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'choose_a_theme');
        break;
    }
    $stepLabels[INFO] = xarML('Overview');
    $stepURLs[INFO] = xarModURL('translations', 'admin', 'generate_trans_info');
    $stepLabels[GEN] = xarML('Generation');
    $stepURLs[GEN] = xarModURL('translations', 'admin', 'generate_trans');
    $stepLabels[REL] = xarML('Release');
    $stepURLs[REL] = NULL;
    //$stepURLs[REL] = xarModURL('translations', 'admin', 'generate_release');
    //$stepLabels[DOWNLOAD] = xarML('Download');
    //$stepURLs[DOWNLOAD] = NULL;

    return array('stepLabels'=>$stepLabels, 'stepURLs'=>$stepURLs, 'currentStep'=>$currentStep);
}

function &translations_create_opbar($currentOp)
{
    $dnType = xarSessionGetVar('translations_dnType');
    $dnName = xarSessionGetVar('translations_dnName');

    // Overview | Generate skels | Translate | Generate translations | Release translations package
    $opLabels[OVERVIEW] = xarML('Overview');
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
    $opLabels[GEN_SKELS] = xarML('Generate skels');
    $opURLs[GEN_SKELS] = xarModURL('translations', 'admin', 'generate_skels_info');
    $opLabels[TRANSLATE] = xarML('Translate');
    $opURLs[TRANSLATE] = xarModURL('translations', 'admin', 'translate');
    //$opLabels[GEN_TRANS] = xarML('Generate translations');
    //$opURLs[GEN_TRANS] = xarModURL('translations', 'admin', 'generate_trans_info');
    //$opLabels[RELEASE] = xarML('Release translations package');
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

function translations_create_trabar($subtype, $subname, $backend=NULL)
{
   @set_time_limit(0);
    $time = explode(' ', microtime());
    $startTime = $time[1] + $time[0];

    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $dnType = xarSessionGetVar('translations_dnType');
    $dnName = xarSessionGetVar('translations_dnName');

    $currentTra = -1;
    switch ($dnType) {
        case XARMLS_DNTYPE_CORE:
        $subtypes = array();
        $subnames = array();
        $entrydata = array();

        $args = array();
        $args['subtype'] = 'core:';
        $args['subname'] = 'core';
        $selectedsubtype = 'core:';
        $selectedsubname = 'core';
        $entry = xarModAPIFunc('translations','admin','getcontextentries',$args);
        if ($entry['numEntries']+$entry['numKeyEntries'] > 0) {
             $entrydata[] = $entry;
             $subtypes[] = 'core:';
             $subnames[] = 'core';
        }
        break;
        case XARMLS_DNTYPE_MODULE:

        $sessmodid = xarSessionGetVar('translations_modid');
        if (!xarVarFetch('modid', 'id', $modid, $sessmodid)) return;
        xarSessionSetVar('translations_modid', $modid);

        if (!$modinfo = xarModGetInfo($modid)) return;
        $modname = $modinfo['name'];
        $moddir = $modinfo['osdirectory'];

        $selectedsubtype = $subtype;
        $selectedsubname = $subname;

        $module_contexts_list[] = 'modules:'.$modname.'::common';

        $subnames = xarModAPIFunc('translations','admin','get_module_phpfiles',array('moddir'=>$moddir));
        foreach ($subnames as $subname) {
            $module_contexts_list[] = 'modules:'.$modname.'::'.$subname;
        }

        $dirnames = xarModAPIFunc('translations','admin','get_module_dirs',array('moddir'=>$moddir));
        foreach ($dirnames as $dirname) {
            if (!preg_match('!^templates!i', $dirname, $matches))
                $pattern = '/^([a-z0-9\-_]+)\.php$/i';
            else 
                $pattern = '/^([a-z0-9\-_]+)\.xd$/i';
            $subnames = xarModAPIFunc('translations','admin','get_module_files',
                                  array('moddir'=>"modules/$moddir/xar$dirname",'pattern'=>$pattern));
            foreach ($subnames as $subname) {
                $module_contexts_list[] = 'modules:'.$modname.':'.$dirname.':'.$subname;
            }
        }

        $subtypes = array();
        $subnames = array();
        $entrydata = array();
        foreach ($module_contexts_list as $module_context) {
            list ($dntype1, $dnname1, $ctxtype1, $ctxname1) = explode(':',$module_context);
            $args = array();
            $ctxtype2 = 'modules:'.$ctxtype1;
            $args['subtype'] = $ctxtype2;
            $args['subname'] = $ctxname1;
            $entry = xarModAPIFunc('translations','admin','getcontextentries',$args);
            if ($entry['numEntries']+$entry['numKeyEntries'] > 0) {
                $entrydata[] = $entry;
                $subtypes[] = $ctxtype2;
                $subnames[] = $ctxname1;
            }
        }
        break;
        case XARMLS_DNTYPE_THEME:

        $sessthemeid = xarSessionGetVar('translations_themeid');
        if (!xarVarFetch('themeid', 'id', $themeid, $sessthemeid)) return;
        xarSessionSetVar('translations_themeid', $themeid);

        if (!$themeinfo = xarModGetInfo($themeid,'theme')) return;
        $themename = $themeinfo['name'];
        $themedir = $themeinfo['osdirectory'];

        $selectedsubtype = $subtype;
        $selectedsubname = $subname;

        $theme_contexts_list[] = 'themes:'.$themename.'::common';

        $dirnames = xarModAPIFunc('translations','admin','get_theme_dirs',array('themedir'=>$themedir));
        foreach ($dirnames as $dirname) {
            $pattern = '/^([a-z\-_]+)\.xt$/i';
            $subnames = xarModAPIFunc('translations','admin','get_theme_files',
                                  array('themedir'=>"themes/$themedir/$dirname",'pattern'=>$pattern));
            foreach ($subnames as $subname) {
                $theme_contexts_list[] = 'themes:'.$themename.':'.$dirname.':'.$subname;
            }
        }

        $subtypes = array();
        $subnames = array();
        $entrydata = array();
        foreach ($theme_contexts_list as $theme_context) {
            list ($dntype1, $dnname1, $ctxtype1, $ctxname1) = explode(':',$theme_context);
            $args = array();
            $ctxtype2 = 'themes:'.$ctxtype1;
            $args['subtype'] = $ctxtype2;
            $args['subname'] = $ctxname1;
            $entry = xarModAPIFunc('translations','admin','getcontextentries',$args);
            if ($entry['numEntries']+$entry['numKeyEntries'] > 0) {
                $entrydata[] = $entry;
                $subtypes[] = $ctxtype2;
                $subnames[] = $ctxname1;
            }
        }
        break;
    }

    return array('subtypes'=>$subtypes,
                 'subnames'=>$subnames,
                 'entrydata'=>$entrydata,
                 'selectedsubtype'=>$selectedsubtype,
                 'selectedsubname'=>$selectedsubname);
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

function translations__dnType2Name ($tran_type)
{
    switch($tran_type) {
    case XARMLS_DNTYPE_CORE:
        return xarML('core');
        break;
    case XARMLS_DNTYPE_MODULE:
        return xarML('module');
        break;
    case XARMLS_DNTYPE_THEME:
        return xarML('theme');
        break;
    default:
        return xarML('unknown');
    }
}

?>