<?php
/**
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

    if (!xarVarFetch('extid', 'id', $themeid)) return;

    if (!($tplData = xarThemeGetInfo($themeid))) return;
    $tplData['dnType'] = XARMLS_DNTYPE_THEME;
    $tplData['dnName'] = $tplData['directory'];
    $tplData['themeid'] = $themeid;

    $druidbar = translations_create_druidbar(INFO, XARMLS_DNTYPE_THEME, $tplData['directory'], $themeid);
    $opbar = translations_create_opbar(OVERVIEW, XARMLS_DNTYPE_THEME, $tplData['directory'], $themeid);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

?>