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

function translations_adminapi_create_backend_instance($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($interface)');
    assert('isset($locale)');

    if ($interface == 'ReferencesBackend') {
        $bt = xarMod::apiFunc('translations','admin','work_backend_type');
    } elseif ($interface == 'TranslationsBackend') {
        $bt = xarMod::apiFunc('translations','admin','release_backend_type');
    }
    if (!$bt) return;
    switch ($bt) {
    case 'php':
        xarLogMessage("MLS: Creating PHP backend");
        sys::import('xaraya.mlsbackends.php');
        return new xarMLS__PHPTranslationsBackend(array($locale));
    case 'xml':
        xarLogMessage("MLS: Creating XML backend");
        sys::import('xaraya.mlsbackends.xml');
        return new xarMLS__XMLTranslationsBackend(array($locale));
    case 'xml2php':
        xarLogMessage("MLS: Creating XML2PHP backend");
        sys::import('xaraya.mlsbackends.xml2php');
        return new xarMLS__XML2PHPTranslationsBackend(array($locale));
    }
    throw new Exception('Unknown');
}

?>