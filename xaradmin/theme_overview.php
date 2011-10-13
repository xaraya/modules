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