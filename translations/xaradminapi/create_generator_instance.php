<?php

/**
 * File: $Id$
 *
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

?>