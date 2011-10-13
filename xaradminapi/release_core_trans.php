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

function translations_adminapi_release_core_trans($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($locale)');

    if (!$bt = xarMod::apiFunc('translations','admin','release_backend_type')) return;;
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if ($bt != 'php') {
        $msg = xarML('Unsupported backend type \'#(1)\'. Don\'t know how to generate release package for that backend.', $bt);
        throw new Exception($msg);
    }

    $dirpath = "var/locales/$locale/php/core/";
    if (!file_exists($dirpath.'core.php')) {
        $msg = xarML('Before releasing translations package you must first generate translations.');
        $link = array(xarML('Click here to proceed.'), xarModURL('translations', 'admin', 'update_info', array('dntype' => 'core')));
        throw new Exception($msg);
    }

    // return translations_make_package('xaraya', XARCORE_VERSION_NUM, $dirpath, $locale);
    $newargs['basefilename'] = 'xaraya';
    $newargs['version'] = XARCORE_VERSION_NUM;
    $newargs['dirpath'] = $dirpath;
    $newargs['locale'] = $locale;
    $backend = xarMod::apiFunc('translations','admin','make_package',$newargs);
}

?>