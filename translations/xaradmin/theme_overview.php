<?php

/**
 * File: $Id$
 *
 * Theme overview
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_theme_overview()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $sessthemeid = xarSessionGetVar('translations_themeid');
    if (!xarVarFetch('themeid', 'id', $themeid, $sessthemeid)) return;
    xarSessionSetVar('translations_themeid', $themeid);

    if (!($tplData = xarThemeGetInfo($themeid))) return;

    xarSessionSetVar('translations_dnName', $tplData['directory']);

    $druidbar = translations_create_theme_overview_druidbar(INFO);
    $opbar = translations_create_opbar(OVERVIEW);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

?>