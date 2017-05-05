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

function translations_admin_choose_a_module()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $installed = xarMod::apiFunc('modules', 'admin', 'getlist', array('filter' => array('State' => XARMOD_STATE_INSTALLED)));
    if (!isset($installed)) return;
    $uninstalled = xarMod::apiFunc('modules', 'admin', 'getlist', array('filter' => array('State' => XARMOD_STATE_UNINITIALISED)));
    if (!isset($uninstalled)) return;

    $modlist1 = array();
    foreach($uninstalled as $term) $modlist1[$term['name']] = $term;
    $modlist2 = array();
    foreach($installed as $term) $modlist2[$term['name']] = $term;
    $modlist = array_merge($modlist1,$modlist2);
    ksort($modlist);

    $tplData = translations_create_druidbar(CHOOSE, xarMLS::DNTYPE_MODULE, '', 0);
    $tplData['modlist'] = $modlist;
    $tplData['dnType'] = xarMLS::DNTYPE_MODULE;
    return $tplData;
}

?>