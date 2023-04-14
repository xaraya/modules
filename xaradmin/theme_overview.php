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
    if(!xarSecurity::check('AdminTranslations')) return;

    if (!xarVar::fetch('extid', 'id', $themeid)) return;

    if (!($tplData = xarTheme::getInfo($themeid))) return;
    $tplData['dnType'] = xarMLS::DNTYPE_THEME;
    $tplData['dnName'] = $tplData['directory'];
    $tplData['themeid'] = $themeid;

    $druidbar = translations_create_druidbar(INFO, xarMLS::DNTYPE_THEME, $tplData['directory'], $themeid);
    $opbar = translations_create_opbar(OVERVIEW, xarMLS::DNTYPE_THEME, $tplData['directory'], $themeid);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

?>