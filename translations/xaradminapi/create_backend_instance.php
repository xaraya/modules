<?php

/**
 * File: $Id$
 *
 * Create backend instance
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
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

?>