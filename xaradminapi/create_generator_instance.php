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

function translations_adminapi_create_generator_instance($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert(isset($interface));
    assert(isset($locale));

    if ($interface == 'ReferencesGenerator') {
        $bt = xarMod::apiFunc('translations','admin','work_backend_type');
    } elseif ($interface == 'TranslationsGenerator') {
        $bt = xarMod::apiFunc('translations','admin','release_backend_type');
    }
    if (!$bt) return;
    switch ($bt) {
        case 'php':
            sys::import('modules.translations.class.PHPTransGenerator');
            return new PHPTranslationsGenerator($locale);
        case 'xml':
            sys::import('modules.translations.class.XMLTransSkelsGenerator');
            return new XMLTranslationsSkelsGenerator($locale);
        case 'xml2php':
            sys::import('modules.translations.class.PHPTransGenerator');
            return new PHPTranslationsGenerator($locale);
    }
    throw new Exception('Unknown');
}

?>