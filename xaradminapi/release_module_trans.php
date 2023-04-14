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
    assert(isset($modid) && isset($locale));

    if (!($modinfo = xarMod::getInfo($modid))) return;
    $modname = $modinfo['name'];
    $modversion = $modinfo['version'];

    if (!$bt = xarMod::apiFunc('translations','admin','release_backend_type')) return;;

    // Security Check
    if(!xarSecurity::check('AdminTranslations')) return;

    if ($bt != 'php') {
        $msg = xarML('Unsupported backend type \'#(1)\'. Don\'t know how to generate release package for that backend.', $bt);
        throw new Exception($msg);
    }

    $dirpath = sys::code() . "var/locales/$locale/php/modules/$modname/";
    if (!file_exists($dirpath.'common.php')) {
        $msg = xarML('Before releasing translations package you must first generate translations.');
        $link = array(xarML('Click here to proceed.'), xarController::URL('translations', 'admin', 'update_info', array('dntype' => 'module')));
        throw new Exception($msg);
    }

    $newargs['basefilename'] = $modname;
    $newargs['version'] = $modversion;
    $newargs['dirpath'] = $dirpath;
    $newargs['locale'] = $locale;
    $releaseBackend = xarMod::apiFunc('translations','admin','make_package',$newargs);

    return $releaseBackend;
}

?>