<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Marco Canini
// Purpose of file: translations admin API
// ----------------------------------------------------------------------

/* API */function translations_adminapi_archiver_path($args = NULL)
{
    static $archiver_path = NULL;
    if (isset($args['archiver_path'])) {
        $archiver_path = $args['archiver_path'];
    } elseif ($archiver_path == NULL) {
        $archiver_path = xarModGetVar('translations', 'archiver_path');
    }
    return $archiver_path;
}

/* API */function translations_adminapi_archiver_flags($args = NULL)
{
    static $archiver_flags = NULL;
    if (isset($args['archiver_flags'])) {
        $archiver_flags = $args['archiver_flags'];
    } elseif ($archiver_flags == NULL) {
        $archiver_flags = xarModGetVar('translations', 'archiver_flags');
    }
    return $archiver_flags;
}

/* API */function translations_adminapi_work_backend_type($args = NULL)
{
    static $type = NULL;
    if (isset($args['type'])) {
        $type = $args['type'];
    } elseif ($type == NULL) {
        $type = xarModGetVar('translations', 'work_backend_type');
    }
    return $type;
}

/* API */function translations_adminapi_release_backend_type($args = NULL)
{
    static $type = NULL;
    if (isset($args['type'])) {
        $type = $args['type'];
    } elseif ($type == NULL) {
        $type = xarModGetVar('translations', 'release_backend_type');
    }
    return $type;
}

/* API */function translations_adminapi_create_backend_instance($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($interface)');
    assert('isset($locale)');

    if ($interface == 'ReferencesBackend') {
        $bt = xarModAPIFunc('translations','admin','work_backend_type');
    } elseif ($interface == 'TranslationsBackend') {
        $bt = xarModAPIFunc('translations','admin','release_backend_type');
    }
    if (!$bt) return;
    switch ($bt) {
        case 'php':
        return new xarMLS__PHPTranslationsBackend(array($locale));
        case 'xml':
        include_once 'includes/xarMLSXMLBackend.php';
        return new xarMLS__XMLTranslationsBackend(array($locale));
    }
    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
}

/* API */function translations_adminapi_create_generator_instance($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($interface)');
    assert('isset($locale)');

    if ($interface == 'ReferencesGenerator') {
        $bt = xarModAPIFunc('translations','admin','work_backend_type');
    } elseif ($interface == 'TranslationsGenerator') {
        $bt = xarModAPIFunc('translations','admin','release_backend_type');
    }
    if (!$bt) return;
    switch ($bt) {
        case 'php':
        include_once 'modules/translations/class/PHPTransGenerator.php';
        return new PHPTranslationsGenerator($locale);
        case 'xml':
        include_once 'modules/translations/class/XMLTransSkelsGenerator.php';
        return new XMLTranslationsSkelsGenerator($locale);
    }
    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
}

/**
 * generate translations XML skels for the core
 * @param $args['locale'] locale name
 * @returns array
 * @return statistics on generation process
 */
/* API */function translations_adminapi_generate_core_skels($args)
{
    set_time_limit(0);

    // Get arguments
    extract($args);

    // Argument check
    assert('isset($locale)');

    if(!xarSecurityCheck('AdminTranslations')) return;

    include 'modules/translations/class/PHPParser.php';
    include 'modules/translations/class/TPLParser.php';

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
    $core_backend = translations_adminapi_create_backend_instance(array('interface' => 'ReferencesBackend', 'locale' => $locale));
    if (!isset($core_backend)) return;
    if ($core_backend->bindDomain(XARMLS_DNTYPE_CORE, 'xaraya') &&
        !$core_backend->loadContext(XARMLS_CTXTYPE_FILE, 'core')) return;

    // Generate translations skels
    $gen = translations_adminapi_create_generator_instance(array('interface' => 'ReferencesGenerator', 'locale' => $locale));
    if (!isset($gen)) return;
    if (!$gen->bindDomain(XARMLS_DNTYPE_CORE, 'xaraya')) return;
    if (!$gen->create(XARMLS_CTXTYPE_FILE, 'core')) return;

    $statistics['core'] = array('entries'=>0, 'keyEntries'=>0);

    // Avoid creating entries for the same locale (en_US.utf-8)
    // NOTE from voll: I comment this IF because we don't have translation anyway
//    if ($locale != 'en_US.utf-8') {
        foreach ($transEntries as $string => $references) {
            $statistics['core']['entries']++;
            // Get previous translation, it's void if not yet translated
            $translation = $core_backend->translate($string);
            $gen->addEntry($string, $references, $translation);
        }
//    }

    foreach ($transKeyEntries as $key => $references) {
        $statistics['core']['keyEntries']++;
        // Get previous translation, it's void if not yet translated
        $translation = $core_backend->translateByKey($key);
        $gen->addKeyEntry($key, $references, $translation);
    }

    $gen->close();

    $time = explode(' ', microtime());
    $endTime = $time[1] + $time[0];

    return array('time' => $endTime - $startTime, 'statistics' => $statistics);
}

/* API */function translations_adminapi_generate_core_trans($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($locale)');

// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $time = explode(' ', microtime());
    $startTime = $time[1] + $time[0];

    $l = xarLocaleGetInfo($locale);
    if ($l['charset'] == 'utf-8') {
        $ref_locale = $locale;
    } else {
        $l['charset'] = 'utf-8';
        $ref_locale = xarLocaleGetString($l);
    }

    // Load core translations
    $backend = translations_adminapi_create_backend_instance(array('interface' => 'ReferencesBackend', 'locale' => $ref_locale));
    if (!isset($backend)) return;
    if (!$backend->bindDomain(XARMLS_DNTYPE_CORE, 'xaraya')) {
        $msg = xarML('Before generating translations you must first generate skels.');
        $link = array(xarML('Click here to proceed.'), xarModURL('translations', 'admin', 'update_info', array('ctxtype' => 'core')));
        xarExceptionSet(XAR_USER_EXCEPTION, 'MissingSkels', new DefaultUserException($msg, $link));
        return;
    }
    if (!$backend->loadContext(XARMLS_CTXTYPE_FILE, 'core')) return;

    $gen = translations_adminapi_create_generator_instance(array('interface' => 'TranslationsGenerator', 'locale' => $locale));
    if (!isset($gen)) return;
    if (!$gen->bindDomain(XARMLS_DNTYPE_CORE, 'xaraya')) return;
    if (!$gen->create(XARMLS_CTXTYPE_FILE, 'core')) return;

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

/* API */function translations_adminapi_release_core_trans($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($locale)');

    if (!$bt = xarModAPIFunc('translations','admin','release_backend_type')) return;;
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if ($bt != 'php') {
        $msg = xarML('Unsupported backend type \'#(1)\'. Don\'t know how to generate release package for that such backend.', $bt);
        xarExceptionSet(XAR_USER_EXCEPTION, 'UnsupportedReleaseBackend', new DefaultUserException($msg));
        return;
    }

    $dirpath = "var/locales/$locale/php/core/";
    if (!file_exists($dirpath.'core.php')) {
        $msg = xarML('Before releasing translations package you must first generate translations.');
        $link = array(xarML('Click here to proceed.'), xarModURL('translations', 'admin', 'update_info', array('ctxtype' => 'core')));
        xarExceptionSet(XAR_USER_EXCEPTION, 'MissingTranslations', new DefaultUserException($msg, $link));
        return;
    }

    return translations_make_package('xaraya', XARCORE_VERSION_NUM, $dirpath, $locale);
}

/**
 * generate translations XML skels for a specified module
 * @param $args['modid'] module registry identifier
 * @param $args['locale'] locale name
 * @returns array
 * @return statistics on generation process
 */
/* API */function translations_adminapi_generate_module_skels($args)
{
    set_time_limit(0);

    // Get arguments
    extract($args);

    // Argument check
    assert('isset($modid) && isset($locale)');

    if (!$modinfo = xarModGetInfo($modid)) return;
    $modname = $modinfo['name'];
    $moddir = $modinfo['osdirectory'];

    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    include 'modules/translations/class/PHPParser.php';
    include 'modules/translations/class/TPLParser.php';

    $time = explode(' ', microtime());
    $startTime = $time[1] + $time[0];

    // Load core translations
    $core_backend = translations_adminapi_create_backend_instance(array('interface' => 'ReferencesBackend', 'locale' => $locale));
    if (!isset($core_backend)) return;
    if (!$core_backend->bindDomain(XARMLS_DNTYPE_CORE, 'xaraya')) {
        $msg = xarML('Before you can generate skels for the #(1) module, you must first generate skels for the core.', $modname);
        $link = array(xarML('Click here to proceed.'), xarModURL('translations', 'admin', 'update_info', array('ctxtype'=>'core')));
        xarExceptionSet(XAR_USER_EXCEPTION, 'MissingCoreSkels', new DefaultUserException($msg, $link));
        return;
    }
    if (!$core_backend->loadContext(XARMLS_CTXTYPE_FILE, 'core')) return;

    // Parse files
    $transEntriesCollection = array();
    $transKeyEntriesCollection = array();

    $subnames = translations_adminapi_get_module_phpfiles(array('moddir'=>$moddir));
    // $subnames = array('user', 'userapi', 'admin', 'adminapi', 'init');

    foreach ($subnames as $subname) {
        $filename = "modules/$moddir/xar$subname.php";

        if (file_exists($filename)) {
            $parser = new PHPParser();
            $parser->parse($filename);

            $transEntriesCollection[$subname] = $parser->getTransEntries();
            $transKeyEntriesCollection[$subname] = $parser->getTransKeyEntries();
        }

        //$partnames = array();
        if (file_exists("modules/$moddir/xar$subname")) {
            $dd = opendir("modules/$moddir/xar$subname");
            while ($filename = readdir($dd)) {
                if (!preg_match('/^([a-z\-_]+)\.php$/i', $filename, $matches)) continue;
                //$partnames[] = $matches[1];

                $parser = new PHPParser();
                $parser->parse("modules/$moddir/xar$subname/$filename");

                $transEntriesCollection[$subname] = $parser->getTransEntries();
                $transKeyEntriesCollection[$subname] = $parser->getTransKeyEntries();
            }
            closedir($dd);
        }
    }

    $tplnames = array();
    if (file_exists("modules/$moddir/xartemplates")) {
        $dd = opendir("modules/$moddir/xartemplates");
        while ($filename = readdir($dd)) {
            if (!preg_match('/^([a-z\-_]+)\.xd$/i', $filename, $matches)) continue;
            $tplnames[] = $matches[1];

            $parser = new TPLParser();
            $parser->parse("modules/$moddir/xartemplates/$filename");

            $transEntriesCollection['template::'.$matches[1]] = $parser->getTransEntries();
            $transKeyEntriesCollection['template::'.$matches[1]] = $parser->getTransKeyEntries();
        }
        closedir($dd);
    }

    $incltplnames = array();
    if (file_exists("modules/$moddir/xartemplates/includes")) {
        $dd = opendir("modules/$moddir/xartemplates/includes");
        while ($filename = readdir($dd)) {
            if (!preg_match('/^([a-z\-_]+)\.xd$/i', $filename, $matches)) continue;
            $incltplnames[] = $matches[1];

            $parser = new TPLParser();
            $parser->parse("modules/$moddir/xartemplates/includes/$filename");

            $transEntriesCollection['incltempl::'.$matches[1]] = $parser->getTransEntries();
            $transKeyEntriesCollection['incltempl::'.$matches[1]] = $parser->getTransKeyEntries();
        }
        closedir($dd);
    }

    $blktplnames = array();
    if (file_exists("modules/$moddir/xartemplates/blocks")) {
        $dd = opendir("modules/$moddir/xartemplates/blocks");
        while ($filename = readdir($dd)) {
            if (!preg_match('/^([a-z\-_]+)\.xd$/i', $filename, $matches)) continue;
            $blktplnames[] = $matches[1];

            $parser = new TPLParser();
            $parser->parse("modules/$moddir/xartemplates/blocks/$filename");

            $transEntriesCollection['blktempl::'.$matches[1]] = $parser->getTransEntries();
            $transKeyEntriesCollection['blktempl::'.$matches[1]] = $parser->getTransKeyEntries();
        }
        closedir($dd);
    }

    $blocknames = array();
    if (file_exists("modules/$moddir/xarblocks")) {
        $dd = opendir("modules/$moddir/xarblocks");
        while ($filename = readdir($dd)) {
            if (!preg_match('/^([a-z\-_]+)\.php$/i', $filename, $matches)) continue;
            $blocknames[] = $matches[1];

            $parser = new PHPParser();
            $parser->parse("modules/$moddir/xarblocks/$filename");

            $transEntriesCollection['block::'.$matches[1]] = $parser->getTransEntries();
            $transKeyEntriesCollection['block::'.$matches[1]] = $parser->getTransKeyEntries();
        }
        closedir($dd);
    }

    $transEntriesCollection = translations_gather_common_entries($transEntriesCollection);
    $transKeyEntriesCollection = translations_gather_common_entries($transKeyEntriesCollection);

    $subnames[] = 'common';
    // Load previously made translations
    $backend = translations_adminapi_create_backend_instance(array('interface' => 'ReferencesBackend', 'locale' => $locale));
    if (!isset($backend)) return;
    if ($backend->bindDomain(XARMLS_DNTYPE_MODULE, $modname)) {
        foreach ($subnames as $subname) {
            if (!$backend->loadContext(XARMLS_CTXTYPE_FILE, $subname)) return;
        }
        foreach ($tplnames as $tplname) {
            if (!$backend->loadContext(XARMLS_CTXTYPE_TEMPLATE, $tplname)) return;
        }
        foreach ($incltplnames as $incltplname) {
            if (!$backend->loadContext(XARMLS_CTXTYPE_INCLTEMPL, $incltplname)) return;
        }
        foreach ($blktplnames as $blktplname) {
            if (!$backend->loadContext(XARMLS_CTXTYPE_BLKTEMPL, $blktplname)) return;
        }
        foreach ($blocknames as $blockname) {
            if (!$backend->loadContext(XARMLS_CTXTYPE_BLOCK, $blockname)) return;
        }
    }

    // Load KEYS
    $filename = "modules/$moddir/KEYS";
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
    $gen = translations_adminapi_create_generator_instance(array('interface' => 'ReferencesGenerator', 'locale' => $locale));
    if (!isset($gen)) return;
    if (!$gen->bindDomain(XARMLS_DNTYPE_MODULE, $modname)) return;

    foreach ($subnames as $subname) {

        if (preg_match('/^template::(.*)/', $subname, $matches)) {
            if (!$gen->create(XARMLS_CTXTYPE_TEMPLATE, $matches[1])) return;
        } elseif (preg_match('/^incltempl::(.*)/', $subname, $matches)) {
            if (!$gen->create(XARMLS_CTXTYPE_INCLTEMPL, $matches[1])) return;
        } elseif (preg_match('/^blktempl::(.*)/', $subname, $matches)) {
            if (!$gen->create(XARMLS_CTXTYPE_BLKTEMPL, $matches[1])) return;
        } elseif (preg_match('/^block::(.*)/', $subname, $matches)) {
            if (!$gen->create(XARMLS_CTXTYPE_BLOCK, $matches[1])) return;
        } else {
            if (!$gen->create(XARMLS_CTXTYPE_FILE, $subname)) return;
        }
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
            // Get the original translation made by developer if any
            if (!$translation && isset($KEYS[$key])) $translation = $KEYS[$key];
            // Add key entry
            $gen->addKeyEntry($key, $references, $translation);
        }

        $gen->close();
    }


    $time = explode(' ', microtime());
    $endTime = $time[1] + $time[0];
    return array('time' => $endTime - $startTime, 'statistics' => $statistics);
}

/**
 * generate translations for the specified module
 * @param $args['modid'] module registry identifier
 * @param $args['locale'] locale name
 * @returns array
 * @return statistics on generation process
 */
/* API */function translations_adminapi_generate_module_trans($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($modid) && isset($locale)');

    if (!($modinfo = xarModGetInfo($modid))) return;
    $modname = $modinfo['name'];
    $moddir = $modinfo['osdirectory'];

// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $time = explode(' ', microtime());
    $startTime = $time[1] + $time[0];

    $l = xarLocaleGetInfo($locale);
    if ($l['charset'] == 'utf-8') {
        $ref_locale = $locale;
    } else {
        $l['charset'] = 'utf-8';
        $ref_locale = xarLocaleGetString($l);
    }

    $backend = translations_adminapi_create_backend_instance(array('interface' => 'ReferencesBackend', 'locale' => $ref_locale));
    if (!isset($backend)) return;
    if (!$backend->bindDomain(XARMLS_DNTYPE_MODULE, $modname)) {
        $msg = xarML('Before generating translations you must first generate skels.');
        $link = array(xarML('Click here to proceed.'), xarModURL('translations', 'admin', 'update_info', array('ctxtype' => 'module')));
        xarExceptionSet(XAR_USER_EXCEPTION, 'MissingSkels', new DefaultUserException($msg, $link));
        return;
    }

    $allCtxNames[XARMLS_CTXTYPE_FILE] = $backend->getContextNames(XARMLS_CTXTYPE_FILE);
    $allCtxNames[XARMLS_CTXTYPE_TEMPLATE] = $backend->getContextNames(XARMLS_CTXTYPE_TEMPLATE);
    $allCtxNames[XARMLS_CTXTYPE_INCLTEMPL] = $backend->getContextNames(XARMLS_CTXTYPE_INCLTEMPL);
    $allCtxNames[XARMLS_CTXTYPE_BLKTEMPL] = $backend->getContextNames(XARMLS_CTXTYPE_BLKTEMPL);
    $allCtxNames[XARMLS_CTXTYPE_BLOCK] = $backend->getContextNames(XARMLS_CTXTYPE_BLOCK);

    $gen = translations_adminapi_create_generator_instance(array('interface' => 'TranslationsGenerator', 'locale' => $locale));
    if (!isset($gen)) return;
    if (!$gen->bindDomain(XARMLS_DNTYPE_MODULE, $modname)) return;

    foreach ($allCtxNames as $ctxType => $ctxNames) {
        foreach ($ctxNames as $ctxName) {
            if (!$backend->loadContext($ctxType, $ctxName)) return;

            if (!$gen->create($ctxType, $ctxName)) return;

            if ($ctxType == XARMLS_CTXTYPE_TEMPLATE) $sName = 'template::'.$ctxName;
            elseif ($ctxType == XARMLS_CTXTYPE_INCLTEMPL) $sName = 'incltempl::'.$ctxName;
            elseif ($ctxType == XARMLS_CTXTYPE_BLKTEMPL) $sName = 'blktempl::'.$ctxName;
            elseif ($ctxType == XARMLS_CTXTYPE_BLOCK) $sName = 'block::'.$ctxName;
            else $sName = $ctxName;

            $statistics[$sName] = array('entries'=>0, 'keyEntries'=>0);

            while (list($string, $translation) = $backend->enumTranslations()) {
                $statistics[$sName]['entries']++;
                $gen->addEntry($string, $translation);
            }

            while (list($key, $translation) = $backend->enumKeyTranslations()) {
                $statistics[$sName]['keyEntries']++;
                $gen->addKeyEntry($key, $translation);
            }

            $gen->close();
            $backend->clear();
        }
    }

    $time = explode(' ', microtime());
    $endTime = $time[1] + $time[0];

    return array('time' => $endTime - $startTime, 'statistics' => $statistics);
}

/**
 * release a translations package
 * @param $args['modid'] module registry identifier
 * @param $args['locale'] locale name
 * @returns string
 * @return the package filename
 */
/* API */function translations_adminapi_release_module_trans($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($modid) && isset($locale)');

    if (!($modinfo = xarModGetInfo($modid))) return;
    $modname = $modinfo['name'];
    $modversion = $modinfo['version'];

    if (!$bt = translations_adminapi_release_backend_type()) return;;
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if ($bt != 'php') {
        $msg = xarML('Unsupported backend type \'#(1)\'. Don\'t know how to generate release package for that such backend.', $bt);
        xarExceptionSet(XAR_USER_EXCEPTION, 'UnsupportedReleaseBackend', new DefaultUserException($msg));
        return;
    }

    $dirpath = "var/locales/$locale/php/modules/$modname/";
    if (!file_exists($dirpath.'common.php')) {
        $msg = xarML('Before releasing translations package you must first generate translations.');
        $link = array(xarML('Click here to proceed.'), xarModURL('translations', 'admin', 'update_info', array('ctxtype' => 'module')));
        xarExceptionSet(XAR_USER_EXCEPTION, 'MissingTranslations', new DefaultUserException($msg, $link));
        return;
    }

    return translations_make_package($modname, $modversion, $dirpath, $locale);
}

/* API */function translations_adminapi_get_module_templates($moddir)
{
    $tplnames = array();
    if (file_exists("modules/$moddir/xartemplates")) {
        $dd = opendir("modules/$moddir/xartemplates");
        while ($filename = readdir($dd)) {
            if (!preg_match('/^([a-zA-Z\-_]+)\.xd$/i', $filename, $matches)) continue;
            $tplnames[] = $matches[1];
        }
        closedir($dd);
    }
    return $tplnames;
}

/* API */function translations_adminapi_get_module_incltempl($moddir)
{
    $tplnames = array();
    if (file_exists("modules/$moddir/xartemplates/includes")) {
        $dd = opendir("modules/$moddir/xartemplates/includes");
        while ($filename = readdir($dd)) {
            if (!preg_match('/^([a-zA-Z\-_]+)\.xd$/i', $filename, $matches)) continue;
            $tplnames[] = $matches[1];
        }
        closedir($dd);
    }
    return $tplnames;
}

/* API */function translations_adminapi_get_module_blktempl($moddir)
{
    $blktplnames = array();
    if (file_exists("modules/$moddir/xartemplates/blocks")) {
        $dd = opendir("modules/$moddir/xartemplates/blocks");
        while ($filename = readdir($dd)) {
            if (!preg_match('/^([a-zA-Z\-_]+)\.xd$/i', $filename, $matches)) continue;
            $blktplnames[] = $matches[1];
        }
        closedir($dd);
    }
    return $blktplnames;
}

/* API */function translations_adminapi_get_module_blocks($moddir)
{
    $blocknames = array();
    if (file_exists("modules/$moddir/xarblocks")) {
        $dd = opendir("modules/$moddir/xarblocks");
        while ($filename = readdir($dd)) {
            if (!preg_match('/^([a-zA-Z\-_]+)\.php$/i', $filename, $matches)) continue;
            $blocknames[] = $matches[1];
        }
        closedir($dd);
    }
    return $blocknames;
}

/* API */function translations_adminapi_get_module_phpfiles($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($moddir)');

    $names = array();
    if (file_exists("modules/$moddir")) {
        $dd = opendir("modules/$moddir");
        while ($filename = readdir($dd)) {
            if (!preg_match('/^([a-z\-_]+)\.php$/i', $filename, $matches)) continue;
            $phpname = $matches[1];
            if ($phpname == 'xarversion') continue;
            if ($phpname == 'xartables') continue;
            $names[] = ereg_replace("^xar","",$phpname);
        }
        closedir($dd);
    }
    $names2 = array('admin','user','adminapi','userapi');
    foreach ($names2 as $name2) {
        if (!file_exists("modules/$moddir/xar$name2.php")) {
            if (file_exists("modules/$moddir/xar$name2")) {
                $names[] = $name2;
            }
        }
    }
    return $names;
}

// PRIVATE STUFF

function translations_make_package($basefilename, $version, $dirpath, $locale)
{
    if (!$archiver_path = xarModAPIFunc('translations','admin','archiver_path')) return;
    if (!file_exists($archiver_path) || !is_executable($archiver_path)) {
        $msg = xarML('Cannot execute \'#(1)\'.', $archiver_path);
        xarExceptionSet(XAR_USER_EXCEPTION, 'UnsupportedReleaseBackend', new DefaultUserException($msg));
        return;
    }
    if (!$archiver_flags = xarModAPIFunc('translations','admin','archiver_flags')) return;

    if (strpos($archiver_path, 'zip') !== false) {
        $ext = 'zip';
    } elseif (strpos($archiver_path, 'tar') !== false) {
        $ext = 'tar';
        if (strpos($archiver_flags, 'z') !== false) {
            $ext .= '.gz';
        } elseif (strpos($archiver_flags, 'j') !== false) {
            $ext .= 'bz2';
        }
    } else {
        $ext = 'unknown';
    }
    $filename = "$basefilename-{$version}_i18n-$locale.$ext";
    $filepath = xarCoreGetVarDirPath().'/cache/'.$filename;

    $archiver_flags = str_replace('%f', $filepath, $archiver_flags);
    $archiver_flags = str_replace('%d', $dirpath, $archiver_flags);

    system("$archiver_path $archiver_flags");

    return $filename;
}


function translations_gather_common_entries($transEntriesCollection)
{
    $commonEntries = array();
    $subnames = array_keys($transEntriesCollection); // ('user', 'userapi', 'admin', 'adminapi');
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