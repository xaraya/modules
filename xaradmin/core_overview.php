<?php
/**
 * Core overview page generation
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_core_overview()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $tplData = translations_create_opbar(OVERVIEW, XARMLS_DNTYPE_CORE, 'xaraya', 0);
    $tplData['verNum'] = xarConfigVars::get(null,'System.Core.VersionNum');
    $tplData['verId'] = xarConfigVars::get(null,'System.Core.VersionId');
    $tplData['verSub'] = xarConfigVars::get(null,'System.Core.VersionSub');
    $tplData['dnType'] = XARMLS_DNTYPE_CORE;
    $tplData['dnName'] = 'xaraya';
    return $tplData;
}

?>