<?php

/**
 * File: $Id$
 *
 * Release module translations
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
 * release a translations package
 * @param $args['modid'] module registry identifier
 * @param $args['locale'] locale name
 * @returns string
 * @return the package filename
 */
function translations_adminapi_release_module_trans($args)
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
        $msg = xarML('Unsupported backend type \'#(1)\'. Don\'t know how to generate release package for that backend.', $bt);
        xarExceptionSet(XAR_USER_EXCEPTION, 'UnsupportedReleaseBackend', new DefaultUserException($msg));
        return;
    }

    $dirpath = "var/locales/$locale/php/modules/$modname/";
    if (!file_exists($dirpath.'common.php')) {
        $msg = xarML('Before releasing translations package you must first generate translations.');
        $link = array(xarML('Click here to proceed.'), xarModURL('translations', 'admin', 'update_info', array('dntype' => 'module')));
        xarExceptionSet(XAR_USER_EXCEPTION, 'MissingTranslations', new DefaultUserException($msg, $link));
        return;
    }

    // return translations_make_package($modname, $modversion, $dirpath, $locale);
    $newargs['basefilename'] = $modname;
    $newargs['version'] = $modversion;
    $newargs['dirpath'] = $dirpath;
    $newargs['locale'] = $locale;
    $backend = xarModAPIFunc('translations','admin','make_package',$newargs);
}

?>
