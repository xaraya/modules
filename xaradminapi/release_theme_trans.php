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
    assert(isset($themeid) && isset($locale));

    if (!($themeinfo = xarMod::getInfo($themeid, 'theme'))) return;
    $themename = $themeinfo['osdirectory'];
    $themeversion = $themeinfo['version'];

    if (!$bt = xarMod::apiFunc('translations','admin','release_backend_type')) return;;

    // Security Check
    if(!xarSecurity::check('AdminTranslations')) return;

    if ($bt != 'php') {
        $msg = xarML('Unsupported backend type \'#(1)\'. Don\'t know how to generate release package for that backend.', $bt);
        throw new Exception($msg);
    }

    $dirpath = "var/locales/$locale/php/themes/$themename/";
    if (!file_exists($dirpath.'common.php')) {
        $msg = xarML('Before releasing translations package you must first generate translations.');
        $link = array(xarML('Click here to proceed.'), xarController::URL('translations', 'admin', 'update_info', array('dntype' => 'theme')));
        throw new Exception($msg);
    }

    $newargs['basefilename'] = $themename;
    $newargs['version'] = $themeversion;
    $newargs['dirpath'] = $dirpath;
    $newargs['locale'] = $locale;
    $releaseBackend = xarMod::apiFunc('translations','admin','make_package',$newargs);

    return $releaseBackend;
}

?>