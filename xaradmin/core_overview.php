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

function translations_admin_core_overview()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $tplData = translations_create_opbar(OVERVIEW, xarMLS::DNTYPE_CORE, 'xaraya', 0);
    $tplData['verNum'] = xarConfigVars::get(null,'System.Core.VersionNum');
    $tplData['verId'] = xarConfigVars::get(null,'System.Core.VersionId');
    $tplData['verSub'] = xarConfigVars::get(null,'System.Core.VersionSub');
    $tplData['dnType'] = xarMLS::DNTYPE_CORE;
    $tplData['dnName'] = 'xaraya';
    return $tplData;
}

?>