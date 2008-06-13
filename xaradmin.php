<?php
/**
 * Translations admin GUI
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/* EVENT */function translations_adminevt_OnModLoad($args)
{
    if (xarMLSGetMode() != XARMLS_UNBOXED_MULTI_LANGUAGE_MODE) {
        $msg = xarML('To execute the translations module you must set the Multi Language System mode to UNBOXED.');
            throw new Exception($msg);
    }
    xarTplSetPageTitle(xarML('Welcome to translators\' paradise!'));

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
    $urlarray = array('dnType'=>$dnType, 'dnName'=>$dnName, 'extid'=>$extid);
    $stepCount = 0;

    switch ($dnType) {
    case XARMLS_DNTYPE_CORE:
        $stepLabels[CHOOSE] = xarML('Core');
        $stepLabels[INFO] = xarML('Overview');
        $stepLabels[GENSKELS] = xarML('Skel. Generation');
        $stepLabels[TRAN] = xarML('Translate');
        $stepLabels[DELFUZZY] = xarML('Delete fuzzy');
        $stepLabels[GENTRANS] = xarML('Trans. Generation');
        $stepLabels[REL] = xarML('Release');
        $stepLabels[DOWNLOAD] = xarML('Download');

        $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'core_overview', $urlarray);
        $stepURLs[INFO] = xarModURL('translations', 'admin', 'core_overview', $urlarray);
        $stepURLs[GENSKELS] = xarModURL('translations','admin','generate_skels_info', $urlarray);
        $stepURLs[TRAN] = xarModURL('translations', 'admin', 'translate', $urlarray);
        $stepURLs[DELFUZZY] = xarModURL('translations','admin','delete_fuzzy', $urlarray);
        $stepURLs[GENTRANS] = xarModURL('translations', 'admin', 'generate_trans_info', $urlarray);
        $stepURLs[REL] = xarModURL('translations', 'admin', 'release_info', $urlarray);

        $stepCount = $currentStep + 1;

        break;
    case XARMLS_DNTYPE_MODULE:
        $stepLabels[CHOOSE] = xarML('Choose a module');
        $stepLabels[INFO] = xarML('Overview');
        $stepLabels[GENSKELS] = xarML('Skel. Generation');
        $stepLabels[TRAN] = xarML('Translate');
        $stepLabels[DELFUZZY] = xarML('Delete fuzzy');
        $stepLabels[GENTRANS] = xarML('Trans. Generation');
        $stepLabels[REL] = xarML('Release');
        $stepLabels[DOWNLOAD] = xarML('Download');

        $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'choose_a_module');
        $stepURLs[INFO] = xarModURL('translations', 'admin', 'module_overview', $urlarray);
        $stepURLs[GENSKELS] = xarModURL('translations','admin','generate_skels_info', $urlarray);
        $stepURLs[TRAN] = xarModURL('translations','admin','translate', $urlarray);
        $stepURLs[DELFUZZY] = xarModURL('translations','admin','delete_fuzzy', $urlarray);
        $stepURLs[GENTRANS] = xarModURL('translations', 'admin', 'generate_trans_info', $urlarray);
        $stepURLs[REL] = xarModURL('translations', 'admin', 'release_info', $urlarray);

        $stepCount = $currentStep + 1;

        break;
    case XARMLS_DNTYPE_THEME:
        $stepLabels[CHOOSE] = xarML('Choose a theme');
        $stepLabels[INFO] = xarML('Overview');
        $stepLabels[GENSKELS] = xarML('Skel. Generation');
        $stepLabels[TRAN] = xarML('Translate');
        $stepLabels[DELFUZZY] = xarML('Delete fuzzy');
        $stepLabels[GENTRANS] = xarML('Trans. Generation');
        $stepLabels[REL] = xarML('Release');
        $stepLabels[DOWNLOAD] = xarML('Download');

        $stepURLs[CHOOSE] = xarModURL('translations', 'admin', 'choose_a_theme');
        $stepURLs[INFO] = xarModURL('translations', 'admin', 'theme_overview', $urlarray);
        $stepURLs[GENSKELS] = xarModURL('translations','admin','generate_skels_info', $urlarray);
        $stepURLs[TRAN] = xarModURL('translations', 'admin', 'translate', $urlarray);
        $stepURLs[DELFUZZY] = xarModURL('translations','admin','delete_fuzzy', $urlarray);
        $stepURLs[GENTRANS] = xarModURL('translations', 'admin', 'generate_trans_info', $urlarray);
        $stepURLs[REL] = xarModURL('translations', 'admin', 'release_info', $urlarray);

        $stepCount = $currentStep + 1;

        break;
    }
    $steps=array();

    $steps= array(
        'stepLabels'=>$stepLabels,
        'stepURLs'=>$stepURLs,
        'currentStep'=>$currentStep,
        'stepCount'=>$stepCount);
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
    $urlarray = array('dnType'=>$dnType, 'dnName'=>$dnName, 'extid'=>$extid);
    // Overview | Generate skels | Translate | Delete fuzzy | Generate translations | Release package
    $opLabels[OVERVIEW] = xarML('Overview');
    switch ($dnType) {
        case XARMLS_DNTYPE_CORE:
        $opURLs[OVERVIEW] = xarModURL('translations', 'admin', 'core_overview', $urlarray);
        break;
        case XARMLS_DNTYPE_MODULE:
        $opURLs[OVERVIEW] = xarModURL('translations', 'admin', 'module_overview', $urlarray);
        break;
        case XARMLS_DNTYPE_THEME:
        $opURLs[OVERVIEW] = xarModURL('translations', 'admin', 'theme_overview', $urlarray);
        break;
    }
    $opLabels[GEN_SKELS] = xarML('Generate skels');
    $opURLs[GEN_SKELS] = xarModURL('translations', 'admin', 'generate_skels_info', $urlarray);
    $opLabels[TRANSLATE] = xarML('Translate');
    $opURLs[TRANSLATE] = xarModURL('translations', 'admin', 'translate', $urlarray);
    $opLabels[DEL_FUZZY] = xarML('Delete fuzzy');
    $opURLs[DEL_FUZZY] = xarModURL('translations', 'admin', 'delete_fuzzy', $urlarray);
    $opLabels[GEN_TRANS] = xarML('Generate translations');
    $opURLs[GEN_TRANS] = xarModURL('translations', 'admin', 'generate_trans_info', $urlarray);
    $opLabels[RELEASE] = xarML('Release package');
    $opURLs[RELEASE] = xarModURL('translations', 'admin', 'release_info', $urlarray);

    // Enables See module details & Generate translations skels
    $enabledOps = array(true, true, false, false, false);

    $locale = translations_working_locale();
    $args['interface'] = 'ReferencesBackend';
    $args['locale'] = $locale;
    $backend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
    if (!isset($backend)) return;

    if ($backend->bindDomain($dnType, $dnName)) {
        $enabledOps[TRANSLATE] = true; // Enables Translate
        $enabledOps[DEL_FUZZY] = true; // Enables Translate
        $enabledOps[GEN_TRANS] = false; // Enables Generate translations
        $args['interface'] = 'TranslationsBackend';
        $args['locale'] = $locale;
        $backend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
        if (!isset($backend)) return;
        if ($backend->bindDomain($dnType, $dnName)) {
            // Enables Release translations package
            $enabledOps[RELEASE] = false;
        }
    }
    $opsData =array();
    $opsData = array('opLabels'=>$opLabels, 'opURLs'=>$opURLs, 'enabledOps'=>$enabledOps, 'currentOp'=>$currentOp);
    return $opsData;
}

function translations_create_trabar($dnType, $dnName, $extid, $subtype, $subname, $backend=NULL)
{
    @set_time_limit(0);
    $time = explode(' ', microtime());
    $startTime = $time[1] + $time[0];

    // Security Check
    if(!xarSecurityCheck('ReadTranslations')) return;

    $currentTra = -1;
    switch ($dnType) {
        case XARMLS_DNTYPE_CORE:
        $subtypes = array();
        $subnames = array();
        $entrydata = array();

        $args = array();
        $args['dntype'] = XARMLS_DNTYPE_CORE;
        $args['dnname'] = 'xaraya';
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

        $modid = $extid;

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
            $args['dntype'] = XARMLS_DNTYPE_MODULE;
            $args['dnname'] = $dnname1;
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
        $themeid = $extid;
        if (!$themeinfo = xarModGetInfo($themeid,'theme')) return;

        // FIXME this is because bug 
        //$themename = $themeinfo['name'];
        $themename = $themeinfo['osdirectory'];

        $themedir = $themeinfo['osdirectory'];

        $selectedsubtype = $subtype;
        $selectedsubname = $subname;

        $theme_contexts_list[] = 'themes:'.$themename.'::common';

        $dirnames = xarModAPIFunc('translations','admin','get_theme_dirs',array('themedir'=>$themedir));
        foreach ($dirnames as $dirname) {
            $pattern = '/^([a-z0-9\-_]+)\.xt$/i';
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
            $args['dntype'] = XARMLS_DNTYPE_THEME;
            $args['dnname'] = $dnname1;
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
    $subData = array();

    $subData = array('subtypes'=>$subtypes,
                 'subnames'=>$subnames,
                 'entrydata'=>$entrydata,
                 'selectedsubtype'=>$selectedsubtype,
                 'selectedsubname'=>$selectedsubname);
    return $subData;
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