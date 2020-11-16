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

function translations_admin_module_overview()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) {
        return;
    }

    if (!xarVarFetch('extid', 'id', $modid)) {
        return;
    }

    if (!($tplData = xarMod::getInfo($modid))) {
        return;
    }
    $tplData['dnType'] = xarMLS::DNTYPE_MODULE;
    $tplData['dnName'] = $tplData['name'];
    $tplData['modid'] = $modid;

    $druidbar = translations_create_druidbar(INFO, xarMLS::DNTYPE_MODULE, $tplData['name'], $modid);
    $opbar = translations_create_opbar(OVERVIEW, xarMLS::DNTYPE_MODULE, $tplData['name'], $modid);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}
