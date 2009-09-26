<?php
/**
 * Create generator instance
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_adminapi_create_generator_instance($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($interface)');
    assert('isset($locale)');

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