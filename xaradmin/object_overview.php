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

function translations_admin_object_overview()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('extid', 'id', $objectid)) return;

    $tplData['object'] = xarMod::apiFunc('dynamicdata', 'user', 'getobject', array('objectid' => $objectid));
    if (!is_object($tplData['object'])) return;
    $tplData['dnType'] = xarMLS::DNTYPE_OBJECT;
//    $tplData['dnName'] = $tplData['directory'];
    $tplData['objectid'] = $objectid;

    $druidbar = translations_create_druidbar(INFO, xarMLS::DNTYPE_OBJECT, $tplData['object']->name, $objectid);
    $opbar = translations_create_opbar(OVERVIEW, xarMLS::DNTYPE_OBJECT, $tplData['object']->name, $objectid);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

?>