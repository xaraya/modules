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
        xarLogMessage("MLS: Creating PHP backend");
        return new xarMLS__PHPTranslationsBackend(array($locale));
    case 'xml':
        xarLogMessage("MLS: Creating XML backend");
        // FIXME: why does this come from core and php backend does not?
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

//  This function returns an array containing all the php files
//  in a given directory that start with "xar"
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
//            if (is_dir("modules/$moddir/$filename") && (substr($filename,0,3) == "xar")) {
//                $names[] = ereg_replace("^xar","",$filename);
//                continue;
//            }
            if (!preg_match('/^([a-z\-_]+)\.php$/i', $filename, $matches)) continue;
            $phpname = $matches[1];
            if ($phpname == 'xarversion') continue;
            if ($phpname == 'xartables') continue;
            $names[] = ereg_replace("^xar","",$phpname);
        }
        closedir($dd);
    }
//  no longer applicable
/*
    $names2 = array('admin','user','adminapi','userapi');
    foreach ($names2 as $name2) {
        if (!file_exists("modules/$moddir/xar$name2.php")) {
            if (file_exists("modules/$moddir/xar$name2")) {
                $names[] = $name2;
            }
        }
    }
*/
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


?>