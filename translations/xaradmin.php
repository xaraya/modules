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
define('TRAN',2);
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

// PRIVATE STUFF

function translations_create_generate_skels_druidbar($currentStep) {
    $stepLabels[INFO] = xarML('Overview');
    $stepLabels[GEN] = xarML('Generation');
    $stepURLs[INFO] = xarModURL('translations', 'admin', 'generate_skels_info');
    $stepURLs[GEN] = NULL;

    return array('stepLabels'=>$stepLabels, 'stepURLs'=>$stepURLs, 'currentStep'=>$currentStep);
}

function translations_create_translate_druidbar($currentStep) {
    $stepLabels[INFO] = xarML('Overview');
    $stepLabels[GEN] = xarML('Generation');
    $stepLabels[TRAN] = xarML('Translate');
    $stepURLs[INFO] = xarModURL('translations', 'admin', 'generate_skels_info');
    $stepURLs[GEN] = xarModURL('translations','admin','generate_skels');

    return array('stepLabels'=>$stepLabels, 'stepURLs'=>$stepURLs, 'currentStep'=>$currentStep);
}

function translations_create_generate_trans_druidbar($currentStep) {
    $stepLabels[INFO] = xarML('Overview');
    $stepLabels[GEN] = xarML('Generation');
    $stepLabels[REL] = xarML('Release');
    //$stepLabels[DOWNLOAD] = xarML('Download');
    $stepURLs[INFO] = xarModURL('translations', 'admin', 'generate_trans_info');
    $stepURLs[GEN] = xarModURL('translations', 'admin', 'generate_trans');
    //$stepURLs[REL] = xarModURL('translations', 'admin', 'generate_release');
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
    //$opLabels[GEN_TRANS] = xarML('Generate translations');
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
    //$opURLs[GEN_TRANS] = xarModURL('translations', 'admin', 'generate_trans_info');
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

        $args = array();
        $args['subtype'] = "file";
        $subnams = $subnames;

        $j = 0;
        foreach ($subnams as $subnameinlist) {
            $args['subname'] = $subnameinlist;
            $entry = xarModAPIFunc('translations','admin','getcontextentries',$args);
            if ($entry['numEntries']+$entry['numKeyEntries'] > 0) {
                array_splice($subnames,$j,1);
                $traLabels[$j] = $subnameinlist;
                $traURLs[$j] = xarModURL('translations', 'admin', 'translate_subtype', array('subtype'=>'file', 'subname'=>$subnameinlist));
                $enabledTras[$j] = true;
                $j++;
            }
        }

        if ($backend == NULL) {
            $locale = translations_working_locale();
            $args['interface'] = 'ReferencesBackend';
            $args['locale'] = $locale;
            $backend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
            if (!isset($backend)) return;
            if (!$backend->bindDomain($dnType, $dnName)) {
                xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
                return;
            }
        }
        $contexts = $GLOBALS['MLS']->getContexts();
        foreach ($contexts as $context) {
            if ($context->getName() != "file" && count($backend->getContextNames($context->getType())) >0) {
                $traLabels[$j] = $context->getLabel();
                $enabledTras[$j] = true;
                $traURLs[$j] = xarModURL('translations', 'admin', 'translate_context',array('name'=>$context->getName()));
                $j++;
            }
        }

        if ($subtype !="") {
            if($subtype =="file") {
                $currentTra = array_search($subname, $traLabels);
            }
            else {
                $currentContext = $GLOBALS['MLS']->getContextByName($subtype);
                $currentTra = array_search($currentContext->getLabel(), $traLabels);
            }
        }
        else {
            $currentTra = 99;
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