<?php

/**
 * File: $Id$
 *
 * Module overview
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_module_overview()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $sessmodid = xarSessionGetVar('translations_modid');
    if (!xarVarFetch('modid', 'id', $modid, $sessmodid)) return;
    xarSessionSetVar('translations_modid', $modid);

    if (!($tplData = xarModGetInfo($modid))) return;

    xarSessionSetVar('translations_dnName', $tplData['name']);

    $druidbar = translations_create_module_overview_druidbar(INFO);
    $opbar = translations_create_opbar(OVERVIEW);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

?>