<?php
/**
 * Release theme translations
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
 * @param $args['themeid'] theme registry identifier
 * @param $args['locale'] locale name
 * @returns string
 * @return the package filename
 */
function translations_adminapi_release_theme_trans($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($themeid) && isset($locale)');

    if (!($themeinfo = xarModGetInfo($themeid, 'theme'))) return;
    $themename = $themeinfo['osdirectory'];
    $themeversion = $themeinfo['version'];

    if (!$bt = xarModAPIFunc('translations','admin','release_backend_type')) return;;

    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if ($bt != 'php') {
        $msg = xarML('Unsupported backend type \'#(1)\'. Don\'t know how to generate release package for that backend.', $bt);
        throw new Exception($msg);
    }

    $dirpath = "var/locales/$locale/php/themes/$themename/";
    if (!file_exists($dirpath.'common.php')) {
        $msg = xarML('Before releasing translations package you must first generate translations.');
        $link = array(xarML('Click here to proceed.'), xarModURL('translations', 'admin', 'update_info', array('dntype' => 'theme')));
        throw new Exception($msg);
    }

    $newargs['basefilename'] = $themename;
    $newargs['version'] = $themeversion;
    $newargs['dirpath'] = $dirpath;
    $newargs['locale'] = $locale;
    $releaseBackend = xarModAPIFunc('translations','admin','make_package',$newargs);

    return $releaseBackend;
}

?>