<?php

/**
 * File: $Id$
 *
 * Translate context
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_translate_context()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('name', 'isset', $name)) return;
    $context = $GLOBALS['MLS']->getContextByName($name);

    $dnType = xarSessionGetVar('translations_dnType');
    $dnName = xarSessionGetVar('translations_dnName');

    $locale = translations_working_locale();

    $args['interface'] = 'ReferencesBackend';
    $args['locale'] = $locale;
    $backend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
    if (!isset($backend)) return;
    if (!$backend->bindDomain($dnType, $dnName)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        return;
    }
    $subtype = $context->getType();
    $subnames = $backend->getContextNames($context->getType());
    $args = array();
    $entrydata = array();
    $subnams = $subnames;
    $subnames = array();
    $args['subtype'] = $context->getName();
    foreach($subnams as $subname) {
        $args['subname'] = $subname;
        $entry = xarModAPIFunc('translations','admin','getcontextentries',$args);
        if ($entry['numEntries']+$entry['numKeyEntries'] > 0) {
            $entrydata[] = $entry;
            $subnames[] = $subname;
        }
    }
    $tplData['subnames'] = $subnames;
    $tplData['entrydata'] = $entrydata;
    $tplData['subtype'] = $context->getName();

    $opbar = translations_create_opbar(TRANSLATE);
    $trabar = translations_create_trabar($name, '',$backend);

    $tplData = array_merge($tplData, $opbar, $trabar);
    $tplData['dnType'] = translations__dnType2Name($dnType);

    return xarTplModule('translations','admin', 'translate_template',$tplData);
}

?>