<?php
/**
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

    if (!xarVarFetch('extid', 'id', $modid)) return;

    if (!($tplData = xarMod::getInfo($modid))) return;
    $tplData['dnType'] = XARMLS_DNTYPE_MODULE;
    $tplData['dnName'] = $tplData['name'];
    $tplData['modid'] = $modid;

    $druidbar = translations_create_druidbar(INFO, XARMLS_DNTYPE_MODULE, $tplData['name'], $modid);
    $opbar = translations_create_opbar(OVERVIEW, XARMLS_DNTYPE_MODULE, $tplData['name'], $modid);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

?>